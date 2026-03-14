<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeReviewRequest;
use App\Models\Trade;
use App\Models\TradeReview;
use Illuminate\Support\Facades\Auth;

class TradeReviewController extends Controller
{
    public function store(TradeReviewRequest $request, Trade $trade)
    {
        $userId = Auth::id();

        if (!$trade->isParticipant($userId)) {
            abort(403);
        }

        if (!in_array($trade->status, ['buyer_completed', 'completed'], true)) {
            return redirect()
                ->route('trades.show', $trade)
                ->with('error', '取引完了後に評価できます。');
        }

        $alreadyReviewed = TradeReview::where('trade_id', $trade->id)
            ->where('reviewer_id', $userId)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()
                ->route('trades.show', $trade)
                ->with('error', 'この取引はすでに評価済みです。');
        }

        $revieweeId = $trade->seller_id === $userId
            ? $trade->buyer_id
            : $trade->seller_id;

        TradeReview::create([
            'trade_id'    => $trade->id,
            'reviewer_id' => $userId,
            'reviewee_id' => $revieweeId,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
        ]);

        $reviewCount = $trade->reviews()->count();

        if ($reviewCount >= 2 && $trade->status !== 'completed') {
            $trade->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return redirect()
            ->route('items.index')
            ->with('success', '評価を送信しました。');
    }
}