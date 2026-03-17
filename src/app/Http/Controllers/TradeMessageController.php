<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeMessageRequest;
use App\Models\Trade;
use App\Models\TradeMessage;
use Illuminate\Support\Facades\Auth;

class TradeMessageController extends Controller
{
    public function store(TradeMessageRequest $request, Trade $trade)
    {
        $userId = Auth::id();

        if (!$trade->isParticipant($userId)) {
            abort(403);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('trade_messages', 'public');
        }

        TradeMessage::create([
            'trade_id'   => $trade->id,
            'user_id'    => $userId,
            'message'    => $request->message,
            'image_path' => $imagePath,
        ]);

        return redirect()
            ->route('trades.show', $trade)
            ->with('success', 'メッセージを送信しました。')
            ->with('message_sent', true);
    }

    public function edit(TradeMessage $message)
    {
        $userId = Auth::id();

        $message->load('trade');

        if ($message->user_id !== $userId) {
            abort(403);
        }

        if (!$message->trade->isParticipant($userId)) {
            abort(403);
        }

        return view('trades.messages.edit', compact('message'));
    }

    public function update(TradeMessageRequest $request, TradeMessage $message)
    {
        $userId = Auth::id();

        $message->load('trade');

        if ($message->user_id !== $userId) {
            abort(403);
        }

        if (!$message->trade->isParticipant($userId)) {
            abort(403);
        }

        $imagePath = $message->image_path;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('trade_messages', 'public');
        }

        $message->update([
            'message'    => $request->message,
            'image_path' => $imagePath,
            'edited_at'  => now(),
        ]);

        return redirect()
            ->route('trades.show', $message->trade)
            ->with('success', 'メッセージを更新しました。');
    }

    public function destroy(TradeMessage $message)
    {
        $userId = Auth::id();

        $message->load('trade');

        if ($message->user_id !== $userId) {
            abort(403);
        }

        if (!$message->trade->isParticipant($userId)) {
            abort(403);
        }

        $trade = $message->trade;

        $message->delete();

        return redirect()
            ->route('trades.show', $trade)
            ->with('success', 'メッセージを削除しました。');
    }
}