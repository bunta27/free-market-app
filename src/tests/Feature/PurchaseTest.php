<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\SoldItem;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_購入ボタン押下で購入が完了する()
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
        $item->name         = '購入テスト商品';
        $item->price        = 5000;
        $item->description  = '購入機能テスト用の商品';
        $item->img_url      = 'items/purchase-test.jpg';
        $item->user_id      = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        $this->assertEquals(0, SoldItem::count());

        $responseIndex = $this->actingAs($buyer)
            ->get(route('purchase.index', ['item_id' => $item->id]));

        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('購入手続き');

        $responseExecute = $this->actingAs($buyer)
            ->post(route('purchase.execute', ['item_id' => $item->id]), [
                'payment' => 1,
            ]);

        $responseExecute->assertStatus(302);

        $this->assertDatabaseHas('sold_items', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_購入済み商品は商品一覧で_sold_ラベル表示される()
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();

        $profile = new Profile();
        $profile->user_id  = $buyer->id;
        $profile->postcode = '234-5678';
        $profile->address  = 'テスト県テスト市2-2-2';
        $profile->building = 'テストマンション202';
        $profile->save();

        $condition = new Condition();
        $condition->condition = '中古';
        $condition->save();

        $item = new Item();
        $item->name         = '一覧soldテスト商品';
        $item->price        = 4000;
        $item->description  = '一覧soldテスト';
        $item->img_url      = 'items/index-sold-test.jpg';
        $item->user_id      = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        SoldItem::create([
            'user_id'          => $buyer->id,
            'item_id'          => $item->id,
            'sending_postcode' => $profile->postcode,
            'sending_address'  => $profile->address,
            'sending_building' => $profile->building,
        ]);

        $response = $this->actingAs($buyer)->get('/');

        $response->assertStatus(200);

        $response->assertSee('item__img sold');
        $response->assertSee('一覧soldテスト商品');
    }

    public function test_購入した商品がプロフィール購入一覧に追加される()
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();

        $profile = new Profile();
        $profile->user_id  = $buyer->id;
        $profile->postcode = '345-6789';
        $profile->address  = 'テスト県テスト市3-3-3';
        $profile->building = 'テストハイツ303';
        $profile->save();

        $condition = new Condition();
        $condition->condition = '美品';
        $condition->save();

        $item = new Item();
        $item->name         = 'プロフィール購入一覧テスト商品';
        $item->price        = 6000;
        $item->description  = 'プロフィール購入一覧テスト';
        $item->img_url      = 'items/profile-buy-test.jpg';
        $item->user_id      = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        SoldItem::create([
            'user_id'          => $buyer->id,
            'item_id'          => $item->id,
            'sending_postcode' => $profile->postcode,
            'sending_address'  => $profile->address,
            'sending_building' => $profile->building,
        ]);

        $response = $this->actingAs($buyer)->get('/mypage?page=buy');

        $response->assertStatus(200);

        $response->assertSee('プロフィール購入一覧テスト商品');
    }
}
