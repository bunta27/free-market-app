<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

        $hddTrade = Trade::whereHas('item', function ($query) use ($seller1) {
            $query->where('name', 'HDD')->where('user_id', $seller1->id);
        })->firstOrFail();

        $laptopTrade = Trade::whereHas('item', function ($query) use ($seller1) {
            $query->where('name', 'ノートPC')->where('user_id', $seller1->id);
        })->firstOrFail();

        $micTrade = Trade::whereHas('item', function ($query) use ($seller2) {
            $query->where('name', 'マイク')->where('user_id', $seller2->id);
        })->firstOrFail();

        $watchMessages = [
            ['user_id' => $seller2->id, 'message' => '購入しました。よろしくお願いします。'],
            ['user_id' => $seller1->id, 'message' => 'ご購入ありがとうございます。発送準備を進めます。'],
            ['user_id' => $seller2->id, 'message' => 'ありがとうございます。'],
            ['user_id' => $seller2->id, 'message' => '発送予定日を教えていただけますか？'],
        ];

        foreach ($watchMessages as $row) {
            TradeMessage::updateOrCreate(
                [
                    'trade_id' => $watchTrade->id,
                    'user_id'  => $row['user_id'],
                    'message'  => $row['message'],
                ],
                [
                    'image_path' => null,
                    'edited_at'  => null,
                ]
            );
        }

        $hddMessages = [
            ['user_id' => $seller2->id, 'message' => '購入しました。よろしくお願いします。'],
            ['user_id' => $seller1->id, 'message' => 'ありがとうございます。発送準備中です。'],
        ];

        foreach ($hddMessages as $row) {
            TradeMessage::updateOrCreate(
                [
                    'trade_id' => $hddTrade->id,
                    'user_id'  => $row['user_id'],
                    'message'  => $row['message'],
                ],
                [
                    'image_path' => null,
                    'edited_at'  => null,
                ]
            );
        }

        $laptopMessages = [
            ['user_id' => $seller2->id, 'message' => '購入しました。よろしくお願いします。'],
            ['user_id' => $seller1->id, 'message' => 'ありがとうございます。本日発送予定です。'],
        ];

        foreach ($laptopMessages as $row) {
            TradeMessage::updateOrCreate(
                [
                    'trade_id' => $laptopTrade->id,
                    'user_id'  => $row['user_id'],
                    'message'  => $row['message'],
                ],
                [
                    'image_path' => null,
                    'edited_at'  => null,
                ]
            );
        }

        $micMessages = [
            ['user_id' => $seller1->id, 'message' => '購入しました。よろしくお願いします。'],
            ['user_id' => $seller2->id, 'message' => 'ありがとうございます。本日発送しました。'],
            ['user_id' => $seller1->id, 'message' => '受け取りました。問題ありませんでした。'],
        ];

        foreach ($micMessages as $row) {
            TradeMessage::updateOrCreate(
                [
                    'trade_id' => $micTrade->id,
                    'user_id'  => $row['user_id'],
                    'message'  => $row['message'],
                ],
                [
                    'image_path' => null,
                    'edited_at'  => null,
                ]
            );
        }
    }
}
