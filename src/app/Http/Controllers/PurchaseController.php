<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Profile;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            ['postcode' => '', 'address' => '', 'building' => '']
        );

        return view('purchase', compact('item', 'user', 'profile'));
    }

    public function purchase($item_id)
    {
        $user = Auth::user();
        $item = Item::with('soldItem')->findOrFail($item_id);

        if ($item->mine()) {
            return redirect()
                ->route('items.detail', ['item' => $item->id]);
        }

        if ($item->sold()) {
            return redirect()
                ->route('items.detail', ['item' => $item->id]);
        }

        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            ['postcode' => '', 'address' => '', 'building' => '']
        );

        DB::transaction(function () use ($item, $user, $profile) {
            SoldItem::create([
                'item_id'          => $item->id,
                'user_id'          => $user->id,
                'sending_postcode' => $profile->postcode,
                'sending_address'  => $profile->address,
                'sending_building' => $profile->building,
            ]);
        });

        return redirect()->route('items.detail', ['item' => $item->id]);
    }

    public function address($item_id)
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
