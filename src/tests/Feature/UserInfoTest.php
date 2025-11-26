<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Condition;
use App\Models\SoldItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィールページでユーザー情報が取得できる()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        $seller = User::factory()->create();

        $profile = new Profile();
        $profile->user_id  = $user->id;
        $profile->postcode = '123-4567';
        $profile->address  = 'テスト県テスト市1-1-1';
        $profile->building = 'テストマンション101';
        $profile->save();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $sellItem           = new Item();
        $sellItem->name     = '出品した商品テスト';
        $sellItem->price    = 3000;
        $sellItem->description  = '出品一覧テスト用の商品';
        $sellItem->img_url      = 'items/sell-test.jpg';
        $sellItem->user_id      = $user->id;
        $sellItem->condition_id = $condition->id;
        $sellItem->save();

        $buyItem           = new Item();
        $buyItem->name     = '購入した商品テスト';
        $buyItem->price    = 4000;
        $buyItem->description  = '購入一覧テスト用の商品';
        $buyItem->img_url      = 'items/buy-test.jpg';
        $buyItem->user_id      = $seller->id;
        $buyItem->condition_id = $condition->id;
        $buyItem->save();

        SoldItem::create([
            'user_id'          => $user->id,
            'item_id'          => $buyItem->id,
            'sending_postcode' => $profile->postcode,
            'sending_address'  => $profile->address,
            'sending_building' => $profile->building,
        ]);

        $responseSell = $this->actingAs($user)
            ->get(route('mypage', ['page' => 'sell']));

        $responseSell->assertStatus(200);

        $responseSell->assertSee('user__icon');
        $responseSell->assertSee('テストユーザー');

        $responseSell->assertSee('出品した商品テスト');

        $responseBuy = $this->actingAs($user)
            ->get(route('mypage', ['page' => 'buy']));

        $responseBuy->assertStatus(200);

        $responseBuy->assertSee('テストユーザー');

        $responseBuy->assertSee('購入した商品テスト');
    }
}
