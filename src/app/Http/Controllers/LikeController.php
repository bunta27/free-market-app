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
        if ($request->page === 'mylist') {
            $items = Like::where('user_id', Auth::id())->get()->map(function ($like_item) {
                return $like_item->item;
            });
        } else {
            $items = Item::where('user_id', '<>', Auth::id())->get();
        }
        return view('index', compact('items'));
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
        $q = $request->input('query');

        $items = Item::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%");
            })
            ->paginate(20);
        return view('index', [
            'items' => $items,
            'query' => $q,
        ]);
    }
}
