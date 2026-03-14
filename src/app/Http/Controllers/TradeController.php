<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Support\Facades\Auth;
use App\Mail\TradeCompletedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\TradeMessageRead;

class TradeController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $trades = Trade::with(['item', 'seller', 'buyer'])
            ->where(function ($query) use ($userId) {
                $query->where('seller_id', $userId)
                    ->orWhere('buyer_id', $userId);
            })
            ->latest()
            ->get();

        return view('trades.index', compact('trades'));
    }

    public function show(Trade $trade)
    {
        $userId = Auth::id();

        if (!$trade->isParticipant($userId)) {
            abort(403);
        }

        $trade->load([
            'item',
            'seller.profile',
            'buyer.profile',
            'messages.user',
            'reviews',
        ]);

        $otherTrades = Trade::with('item')
            ->where(function ($query) use ($userId) {
                $query->where('seller_id', $userId)
                    ->orWhere('buyer_id', $userId);
            })
            ->where('id', '!=', $trade->id)
            ->whereIn('status', ['ongoing', 'buyer_completed'])
            ->latest()
            ->get();

        $partnerUser = $trade->seller_id === $userId
            ? $trade->buyer
            : $trade->seller;

        $hasMyReview = $trade->reviews->where('reviewer_id', $userId)->isNotEmpty();
        $canComplete = $trade->buyer_id === $userId && $trade->status === 'ongoing';
        $canReview = in_array($trade->status, ['buyer_completed', 'completed'], true) && !$hasMyReview;

        return view('trades.show', compact(
            'trade',
            'otherTrades',
            'partnerUser',
            'hasMyReview',
            'canComplete',
            'canReview'
        ));
    }

    public function complete(Trade $trade)
    {
        $userId = Auth::id();

        if (!$trade->isParticipant($userId)) {
            abort(403);
        }

        if ($trade->buyer_id !== $userId) {
            return redirect()
                ->route('trades.show', $trade)
                ->with('error', '購入者のみ取引完了ができます。');
        }

        if ($trade->status !== 'ongoing') {
            return redirect()
                ->route('trades.show', $trade)
                ->with('error', 'この取引はすでに完了処理済みです。');
        }

        $trade->update([
            'status' => 'buyer_completed',
            'buyer_completed_at' => now(),
        ]);

        $trade->load(['item', 'seller', 'buyer']);

        $messagesToRead = $trade->messages()
            ->where('user_id', '!=', $userId)
            ->get();

        foreach ($messagesToRead as $message) {
            TradeMessageRead::updateOrCreate(
                [
                    'trade_message_id' => $message->id,
                    'user_id' => $userId,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        Mail::to($trade->seller->email)->send(new TradeCompletedMail($trade));

        return redirect()
            ->route('trades.show', $trade)
            ->with('success', '取引を完了しました。評価を入力してください。');
    }
}