<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSelectTest extends TestCase
{
    use RefreshDatabase;

    public function test_支払い方法選択画面で支払い方法が小計欄に正しく表示される()
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();

        $profile = new Profile();
        $profile->user_id  = $buyer->id;
        $profile->postcode = '123-4567';
        $profile->address  = 'テスト県テスト市1-1-1';
        $profile->building = 'テストビル101';
        $profile->save();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $item = new Item();
        $item->name         = '支払い方法テスト商品';
        $item->price        = 3000;
        $item->description  = '支払い方法テスト用の商品';
        $item->img_url      = 'items/payment-test.jpg';
        $item->user_id      = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        $response = $this->actingAs($buyer)
            ->get(route('purchase.index', ['item_id' => $item->id]));

        $response->assertStatus(200);

        $response->assertSee('支払い方法');

        $response->assertSee('コンビニ支払い');
        $response->assertSee('カード支払い');

        $response->assertSee('<td id="method">コンビニ支払い</td>', false);
    }
}
