<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\CategoryItem;
use App\Models\Like;
use App\Models\Condition;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab   = $request->query('page');
        $query = $request->query('query');

        if ($tab === 'mylist') {
            $items = Item::whereHas('likes', function ($likeQuery) {
                    $likeQuery->where('user_id', Auth::id());
                })
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                    });
                })
                ->paginate(20)
                ->appends([
                    'page'  => $tab,
                    'query' => $query,
                ]);
        } else {
            $items = Item::where('user_id', '<>', Auth::id())
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                    });
                })
                ->paginate(20)
                ->appends([
                    'page'  => $tab,
                    'query' => $query,
                ]);
        }

        return view('index', [
            'items' => $items,
            'query' => $query,
            'tab'   => $tab,
        ]);
    }

    public function detail(Item $item)
    {
        $item->load('categories:id,category', 'condition:id,condition', 'user:id,name');

        return view('detail', compact('item'));
    }


    public function sellView()
    {
        $conditions = Condition::all();
        $categories = Category::all();
        return view('sell', compact('conditions', 'categories'));
    }

    public function sellCreate(ItemRequest $request)
    {
        $img_path = $request->file('img_url')->store('items', 'public');

        $item = Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'brand' => $request->brand,
            'description' => $request->description,
            'img_url' => $img_path,
            'condition_id' => $request->condition_id,
            'user_id' => Auth::id(),
        ]);

        foreach ($request->categories as $category_id) {
            CategoryItem::create([
                'item_id' => $item->id,
                'category_id' => $category_id,
            ]);
        }

        return redirect()->route('items.detail', ['item' => $item->id]);

    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $tab   = $request->input('page');

        return redirect()->route('items.index', [
            'page'  => $tab,
            'query' => $query,
        ]);
    }
}
