<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Item;

class Purchase {
    public function handle(Request $request, Closure $next)
    {
        $itemId = $request->route('item_id');
        $item = Item::find($itemId);

        if (!$item) {
            return redirect()->route('items.index')->with('error', '商品が見つかりません。');
        }

        if (Auth::check() && $item->user_id === Auth::id()) {
            return redirect()->route('items.detail', ['item' => $item->id])
                ->with('error', '自分が出品した商品は購入できません。');
        }

        if (method_exists($item, 'sold') && $item->sold()) {
            return redirect()->route('items.detail', ['item' => $item->id])
                ->with('error', 'この商品は売り切れです。');
        }

        return $next($request);
    }
}
