<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Profile;
use App\Models\SoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

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

    public function purchase($item_id, Request $request)
    {
        $user = Auth::user();
        $item = Item::with('soldItem')->findOrFail($item_id);

        if ($item->mine()) {
            return redirect()->route('items.detail', ['item' => $item->id]);
        }

        if ($item->sold()) {
            return redirect()->route('items.detail', ['item' => $item->id]);
        }

        $paymentMethod = $request->input('payment_method', 'card');

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => [$paymentMethod],
            'mode'                 => 'payment',
            'line_items'           => [[
                'price_data' => [
                    'currency'     => 'jpy',
                    'unit_amount'  => $item->price,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $user->email,
            'success_url'    => route('purchase.success', ['item_id' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'     => route('purchase.cancel', ['item_id' => $item->id]),
        ]);
        return redirect($session->url);
    }

    public function success($item_id, Request $request)
    {
        $user = Auth::user();
        $item = Item::with('soldItem')->findOrFail($item_id);

        if ($item->sold()) {
            return redirect()->route('items.detail', ['item' => $item->id]);
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

        return redirect()
            ->route('items.detail', ['item' => $item->id])
            ->with('success', '決済が完了しました。');
    }

    public function cancel($item_id)
    {
        $item = Item::findOrFail($item_id);

        return redirect()
            ->route('purchase.index', ['item_id' => $item->id])
            ->with('error', '決済がキャンセルされました。');
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
