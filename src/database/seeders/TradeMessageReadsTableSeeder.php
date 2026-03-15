<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\TradeMessageRead;

class TradeMessageReadsTableSeeder extends Seeder
{
    public function run(): void
    {
        $seller1 = User::where('email', 'seller1@example.com')->firstOrFail();

        $laptopTrade = Trade::whereHas('item', function ($query) use ($seller1) {
            $query->where('name', 'ノートPC')->where('user_id', $seller1->id);
        })->firstOrFail();

        $messages = TradeMessage::where('trade_id', $laptopTrade->id)
            ->where('user_id', '!=', $seller1->id)
            ->get();

        foreach ($messages as $message) {
            TradeMessageRead::updateOrCreate(
                [
                    'trade_message_id' => $message->id,
                    'user_id' => $seller1->id,
                ],
                [
                    'read_at' => now()->subHour(),
                ]
            );
        }
    }
}
