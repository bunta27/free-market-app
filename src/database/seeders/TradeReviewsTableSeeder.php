<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Trade;
use App\Models\TradeReview;

class TradeReviewsTableSeeder extends Seeder
{
    public function run(): void
    {
        $seller2 = User::where('email', 'seller2@example.com')->firstOrFail();
        $user3   = User::where('email', 'user3@example.com')->firstOrFail();

        $micTrade = Trade::whereHas('item', function ($query) use ($seller2) {
            $query->where('name', 'マイク')->where('user_id', $seller2->id);
        })->firstOrFail();

        TradeReview::updateOrCreate(
            [
                'trade_id'    => $micTrade->id,
                'reviewer_id' => $user3->id,
            ],
            [
                'reviewee_id' => $seller2->id,
                'rating'      => 3,
                'comment'     => '丁寧に対応していただき、安心して取引できました。',
            ]
        );

        TradeReview::updateOrCreate(
            [
                'trade_id'    => $micTrade->id,
                'reviewer_id' => $seller2->id,
            ],
            [
                'reviewee_id' => $user3->id,
                'rating'      => 4,
                'comment'     => 'スムーズにやり取りしていただき、ありがとうございました。',
            ]
        );
    }
}