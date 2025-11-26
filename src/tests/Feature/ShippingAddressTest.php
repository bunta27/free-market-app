<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Condition;
use App\Models\SoldItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィールの住所が購入画面に反映される()
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();

        $profile = new Profile();
        $profile->user_id  = $buyer->id;
        $profile->postcode = '150-0002';
        $profile->address  = '新住所県新住所市2-2-2';
        $profile->building = '新タワー202';
        $profile->save();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $item = new Item();
        $item->name         = '配送先テスト商品';
        $item->price        = 3000;
        $item->description  = '配送先変更テスト用の商品';
        $item->img_url      = 'items/shipping-test.jpg';
        $item->user_id      = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        $response = $this->actingAs($buyer)
            ->get(route('purchase.index', ['item_id' => $item->id]));

        $response->assertStatus(200);

        $response->assertSee('〒 150-0002');
        $response->assertSee('新住所県新住所市2-2-2');
        $response->assertSee('新タワー202');
    }

    public function test_購入した商品に送付先住所が紐づいて保存される()
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();

        $profile = new Profile();
        $profile->user_id  = $buyer->id;
        $profile->postcode = '160-0003';
        $profile->address  = '配達県配達市3-3-3';
        $profile->building = '配達ビル303';
        $profile->save();

        $condition = new Condition();
        $condition->condition = '中古';
        $condition->save();

        $item = new Item();
        $item->name         = '配送先紐付けテスト商品';
        $item->price        = 4500;
        $item->description  = '配送先紐付けテスト';
        $item->img_url      = 'items/shipping-bind-test.jpg';
        $item->user_id      = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        $this->assertEquals(0, SoldItem::count());

        $response = $this->actingAs($buyer)
            ->post(route('purchase.execute', ['item_id' => $item->id]), [
                'payment' => 1,
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('sold_items', [
            'user_id'          => $buyer->id,
            'item_id'          => $item->id,
            'sending_postcode' => $profile->postcode,
            'sending_address'  => $profile->address,
            'sending_building' => $profile->building,
        ]);
    }
}
