<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Trade;
use App\Models\TradeMessageRead;
use App\Models\TradeMessage;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            ['postcode' => '', 'address' => '', 'building' => '', 'img_url' => null]
        );

        return view('profile', compact('user', 'profile'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        if ($request->hasFile('img_url')) {
            $path = $request->file('img_url')->store('profiles', 'public');
        } else {
            $path = $profile->img_url;
        }

        $profile->update([
            'img_url'  => $path,
            'postcode' => $request->postcode,
            'address'  => $request->address,
            'building' => $request->building,
        ]);

        $user->update([
            'name' => $request->name,
        ]);

        return redirect()->route('mypage');
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('page', 'sell');

        if ($tab === 'buy') {
            $items = Item::with('trade')
                ->whereHas('soldItem', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->get();

            $trades = collect();
        } elseif ($tab === 'trade') {
            $trades = Trade::with(['item', 'messages'])
                ->where(function ($query) use ($user) {
                    $query->where('seller_id', $user->id)
                        ->orWhere('buyer_id', $user->id);
                })
                ->whereIn('status', ['ongoing', 'buyer_completed'])
                ->get()
                ->map(function ($trade) use ($user) {
                    $unreadCount = $trade->messages
                        ->where('user_id', '!=', $user->id)
                        ->filter(function ($message) use ($user) {
                            return !TradeMessageRead::where('trade_message_id', $message->id)
                                ->where('user_id', $user->id)
                                ->exists();
                        })
                        ->count();

                    $trade->unread_count = $unreadCount;

                    return $trade;
                })
                ->sortByDesc(function ($trade) {
                    return $trade->unread_count > 0 ? 1 : 0;
                })
                ->values();

            $items = collect();
        } else {
            $items = Item::with('trade')
                ->where('user_id', $user->id)
                ->get();

            $trades = collect();
        }

        $avgRating = method_exists($user, 'reviewAverage')
            ? $user->reviewAverage()
            : null;

        $tradeCount = Trade::with('messages')
            ->where(function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
            ->whereIn('status', ['ongoing', 'buyer_completed'])
            ->get()
            ->sum(function ($trade) use ($user) {
                return $trade->messages
                    ->where('user_id', '!=', $user->id)
                    ->filter(function ($message) use ($user) {
                        return !TradeMessageRead::where('trade_message_id', $message->id)
                            ->where('user_id', $user->id)
                            ->exists();
                    })
                    ->count();
            });

        return view('mypage', compact(
            'user',
            'tab',
            'items',
            'trades',
            'avgRating',
            'tradeCount'
        ));
    }
}