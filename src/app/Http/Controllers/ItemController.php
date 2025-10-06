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
        if ($request->page == 'mylist') {
            $items = Like::where('user_id', Auth::id())->get()->map(function ($like_item) {
                return $like_item->item;
            });
        }
        else {
            $items = Item::where('user_id', '<>', Auth::id())->get();
        }
        return view('index', compact('items'));
    }

    public function detail(Item $item)
    {
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
        $img = $request->file('img_url');

        try {
            $img_url = Storage::disk('local')->put('public/img', $img);
        } catch (\Throwable $th) {
            throw $th;
        }

        $item = Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'img_url' => $img_url,
            'condition_id' => $request->condition_id,
            'user_id' => Auth::id(),
        ]);

        foreach ($request->categories as $category_id) {
            CategoryItem::create([
                'item_id' => $item->id,
                'category_id' => $category_id,
            ]);
        }

        return redirect()->route('item.detail', ['item' => $item->id]);
    }
}
