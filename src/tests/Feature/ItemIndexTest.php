<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\SoldItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_全商品を取得できる()
    {
        $viewer = User::factory()->create();

        $seller = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $items = [];
        for ($i = 0; $i < 3; $i++) {
            $item = new Item();
            $item->name        = 'テスト商品' . $i;
            $item->price       = 1000 + $i;
            $item->description = 'テスト説明';
            $item->img_url     = 'https://example.com/test.jpg';
            $item->user_id     = $seller->id;
            $item->condition_id = $condition->id;
            $item->save();

            $items[] = $item;
        }

        $response = $this->actingAs($viewer)->get('/');

        $response->assertStatus(200);

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_購入済み商品には_sold_ラベルが表示される()
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '中古';
        $condition->save();

        $item = new Item();
        $item->name        = '購入済み商品';
        $item->price       = 3000;
        $item->description = '購入済みテスト';
        $item->img_url     = 'https://example.com/item.jpg';
        $item->user_id     = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        $sold = new SoldItem();
        $sold->user_id          = $buyer->id;
        $sold->item_id          = $item->id;
        $sold->sending_postcode = '123-4567';
        $sold->sending_address  = 'テスト県テスト市1-1-1';
        $sold->sending_building = 'テストビル101';
        $sold->save();

        $response = $this->actingAs($buyer)->get('/');

        $response->assertStatus(200);

        $response->assertSee('購入済み商品');
        $response->assertSee('sold');
    }

    public function test_自分が出品した商品は一覧に表示されない()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '美品';
        $condition->save();

        $myItem = new Item();
        $myItem->name        = '自分の商品';
        $myItem->price       = 5000;
        $myItem->description = '自分が出品した商品';
        $myItem->img_url     = 'https://example.com/my-item.jpg';
        $myItem->user_id     = $user->id;
        $myItem->condition_id = $condition->id;
        $myItem->save();

        $otherUser = User::factory()->create();
        $othersItem = new Item();
        $othersItem->name        = '他人の商品';
        $othersItem->price       = 4000;
        $othersItem->description = '他人が出品した商品';
        $othersItem->img_url     = 'https://example.com/other-item.jpg';
        $othersItem->user_id     = $otherUser->id;
        $othersItem->condition_id = $condition->id;
        $othersItem->save();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);

        $response->assertDontSee('自分の商品');

        $response->assertSee('他人の商品');
    }
}
