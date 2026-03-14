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
        $user3   = User::where('email', 'user3@example.com')->firstOrFail();

        $watch  = Item::where('name', '腕時計')->where('user_id', $seller1->id)->firstOrFail();
        $hdd    = Item::where('name', 'HDD')->where('user_id', $seller1->id)->firstOrFail();
        $laptop = Item::where('name', 'ノートPC')->where('user_id', $seller1->id)->firstOrFail();

        $mic = Item::where('name', 'マイク')->where('user_id', $seller2->id)->firstOrFail();

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
                'item_id' => $hdd->id,
                'user_id' => $user3->id,
            ],
            [
                'sending_postcode' => '150-0001',
                'sending_address'  => '東京都渋谷区神宮前1-1-1',
                'sending_building' => 'サンプルビル202',
            ]
        );

        SoldItem::updateOrCreate(
            [
                'item_id' => $laptop->id,
                'user_id' => $seller2->id,
            ],
            [
                'sending_postcode' => '060-0001',
                'sending_address'  => '北海道札幌市中央区北1条西1丁目',
                'sending_building' => 'テストビル303',
            ]
        );

        SoldItem::updateOrCreate(
            [
                'item_id' => $mic->id,
                'user_id' => $user3->id,
            ],
            [
                'sending_postcode' => '530-0001',
                'sending_address'  => '大阪府大阪市北区梅田1-1',
                'sending_building' => 'サンプルハイツ404',
            ]
        );
    }
}