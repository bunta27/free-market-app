<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;
use App\Models\Profile;

class PurchaseController extends Controller
{
    public function index($item_id, Request $request)
    {
        $item = Item::find($item_id);
        $user = User::find(Auth::id());
        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            ['postcode' => '', 'address' => '', 'building' => '']
        );

        return view('purchase', compact('item', 'user', 'profile'));
    }

    public function purchase($item_id)
    {
        $item = Item::findOrFail($item_id);

        if ($item->user_id !== Auth::id()) {
            return redirect('/');
        }

        // 購入処理

        return redirect()->route('items.detail', ['item' => $item->id]);
    }

    public function address($item_id, Request $request)
    {
        $user = Auth::user();

        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            ['postcode' => '', 'address' => '', 'building' => '']
        );

        return view('address', compact('item_id', 'user', 'profile'));
    }

    public function updateAddress(AddressRequest $request)
    {
        $user = Auth::user();

        Profile::where('user_id', $user->id)->update([
            'postcode' => $request->postcode,
            'address'  => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.index', ['item_id' => $request->item_id]);
    }
}
