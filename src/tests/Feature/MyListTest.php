<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Like;
use App\Models\SoldItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねした商品だけが表示される()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $likedItem = new Item();
        $likedItem->name         = 'いいねした商品';
        $likedItem->price        = 3000;
        $likedItem->description  = 'いいね対象の商品';
        $likedItem->img_url      = 'items/test-liked.jpg';
        $likedItem->user_id      = $user->id;
        $likedItem->condition_id = $condition->id;
        $likedItem->save();

        $notLikedItem = new Item();
        $notLikedItem->name         = 'いいねしていない商品';
        $notLikedItem->price        = 4000;
        $notLikedItem->description  = 'いいねしていない商品';
        $notLikedItem->img_url      = 'items/test-no-liked.jpg';
        $notLikedItem->user_id      = $user->id;
        $notLikedItem->condition_id = $condition->id;
        $notLikedItem->save();

        $like = new Like();
        $like->user_id = $user->id;
        $like->item_id = $likedItem->id;
        $like->save();

        $response = $this->actingAs($user)->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertSee('いいねした商品');

        $response->assertDontSee('いいねしていない商品');
    }

    public function test_マイリスト内の購入済み商品には_sold_クラスが付く()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '中古';
        $condition->save();

        $item = new Item();
        $item->name         = '購入済みいいね商品';
        $item->price        = 5000;
        $item->description  = '購入済みテスト';
        $item->img_url      = 'items/test-sold.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $like = new Like();
        $like->user_id = $user->id;
        $like->item_id = $item->id;
        $like->save();

        $sold = new SoldItem();
        $sold->user_id          = $user->id;
        $sold->item_id          = $item->id;
        $sold->sending_postcode = '123-4567';
        $sold->sending_address  = 'テスト県テスト市1-1-1';
        $sold->sending_building = 'テストビル101';
        $sold->save();

        $response = $this->actingAs($user)->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertSee('購入済みいいね商品');

        $response->assertSee('sold');
    }

    public function test_未ログインの場合はマイリスト商品が表示されない()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $item = new Item();
        $item->name         = 'ゲストには見えない商品';
        $item->price        = 2000;
        $item->description  = 'ゲストテスト用';
        $item->img_url      = 'items/test-guest.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $like = new Like();
        $like->user_id = $user->id;
        $like->item_id = $item->id;
        $like->save();

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('ゲストには見えない商品');
    }
}
