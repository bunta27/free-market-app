<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Like;

class LikeController extends Controller
{
    public function create($item_id)
    {
        Like::firstOrCreate([
            'user_id' => Auth::id(),
            'item_id' => $item_id,
        ]);

        return back();
    }

    public function delete($item_id)
    {
        Like::where('user_id', Auth::id())
            ->where('item_id', $item_id)
            ->delete();

            return back();
    }
}
