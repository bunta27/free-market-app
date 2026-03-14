<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;

class SoldItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $seller1 = User::where('email', 'seller1@example.com')->firstOrFail();
        $seller2 = User::where('email', 'seller2@example.com')->firstOrFail();

        $watch = Item::where('name', '腕時計')->where('user_id', $seller1->id)->firstOrFail();
        $mic   = Item::where('name', 'マイク')->where('user_id', $seller2->id)->firstOrFail();

        SoldItem::updateOrCreate(
            [
                'item_id' => $watch->id,
                'user_id' => $seller2->id,
            ],
            [
                'sending_postcode' => '100-0001',
                'sending_address'  => '東京都千代田区千代田1-1',
                'sending_building' => 'テストマンション101',
            ]
        );

        SoldItem::updateOrCreate(
            [
                'item_id' => $mic->id,
                'user_id' => $seller1->id,
            ],
            [
                'sending_postcode' => '150-0001',
                'sending_address'  => '東京都渋谷区神宮前1-1-1',
                'sending_building' => 'サンプルビル202',
            ]
        );
    }
}