<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Trade;
use App\Models\TradeMessage;

class TradeMessagesTableSeeder extends Seeder
{
    public function run(): void
    {
        $seller1 = User::where('email', 'seller1@example.com')->firstOrFail();
        $seller2 = User::where('email', 'seller2@example.com')->firstOrFail();

        $watchTrade = Trade::whereHas('item', function ($query) use ($seller1) {
            $query->where('name', '腕時計')->where('user_id', $seller1->id);
        })->firstOrFail();

        $micTrade = Trade::whereHas('item', function ($query) use ($seller2) {
            $query->where('name', 'マイク')->where('user_id', $seller2->id);
        })->firstOrFail();

        $watchMessages = [
            ['user_id' => $seller2->id, 'message' => '購入させていただきました。よろしくお願いします。'],
            ['user_id' => $seller1->id, 'message' => 'ご購入ありがとうございます。近日中に発送予定です。'],
            ['user_id' => $seller2->id, 'message' => '承知しました。楽しみにしています。'],
        ];

        foreach ($watchMessages as $index => $row) {
            TradeMessage::updateOrCreate(
                [
                    'trade_id'   => $watchTrade->id,
                    'user_id'    => $row['user_id'],
                    'message'    => $row['message'],
                ],
                [
                    'image_path' => null,
                    'edited_at'  => null,
                    'created_at' => now()->subHours(12 - $index),
                    'updated_at' => now()->subHours(12 - $index),
                ]
            );
        }

        $micMessages = [
            ['user_id' => $seller1->id, 'message' => '購入しました。よろしくお願いします。'],
            ['user_id' => $seller2->id, 'message' => 'ありがとうございます。本日発送しました。'],
            ['user_id' => $seller1->id, 'message' => '受け取りました。問題ありませんでした。'],
        ];

        foreach ($micMessages as $index => $row) {
            TradeMessage::updateOrCreate(
                [
                    'trade_id'   => $micTrade->id,
                    'user_id'    => $row['user_id'],
                    'message'    => $row['message'],
                ],
                [
                    'image_path' => null,
                    'edited_at'  => null,
                    'created_at' => now()->subDays(3)->addHours($index + 9),
                    'updated_at' => now()->subDays(3)->addHours($index + 9),
                ]
            );
        }
    }
}