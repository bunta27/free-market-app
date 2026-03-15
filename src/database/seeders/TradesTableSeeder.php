<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Trade;

class TradesTableSeeder extends Seeder
{
    public function run(): void
    {
        $seller1 = User::where('email', 'seller1@example.com')->firstOrFail();
        $seller2 = User::where('email', 'seller2@example.com')->firstOrFail();

        $watch  = Item::where('name', '腕時計')->where('user_id', $seller1->id)->firstOrFail();
        $hdd    = Item::where('name', 'HDD')->where('user_id', $seller1->id)->firstOrFail();
        $laptop = Item::where('name', 'ノートPC')->where('user_id', $seller1->id)->firstOrFail();
        $mic    = Item::where('name', 'マイク')->where('user_id', $seller2->id)->firstOrFail();

        Trade::updateOrCreate(
            ['item_id' => $watch->id],
            [
                'seller_id' => $seller1->id,
                'buyer_id'  => $seller2->id,
                'status'    => 'ongoing',
                'buyer_completed_at' => null,
                'completed_at'       => null,
            ]
        );

        Trade::updateOrCreate(
            ['item_id' => $hdd->id],
            [
                'seller_id' => $seller1->id,
                'buyer_id'  => $seller2->id,
                'status'    => 'ongoing',
                'buyer_completed_at' => null,
                'completed_at'       => null,
            ]
        );

        Trade::updateOrCreate(
            ['item_id' => $laptop->id],
            [
                'seller_id' => $seller1->id,
                'buyer_id'  => $seller2->id,
                'status'    => 'ongoing',
                'buyer_completed_at' => null,
                'completed_at'       => null,
            ]
        );

        Trade::updateOrCreate(
            ['item_id' => $mic->id],
            [
                'seller_id' => $seller2->id,
                'buyer_id'  => $seller1->id,
                'status'    => 'completed',
                'buyer_completed_at' => now()->subDays(2),
                'completed_at'       => now()->subDay(),
            ]
        );
    }
}
