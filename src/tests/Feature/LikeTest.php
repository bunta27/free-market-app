<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねアイコン押下でいいね登録されカウントが増える()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $item = new Item();
        $item->name         = 'いいねテスト商品';
        $item->price        = 1000;
        $item->description  = 'いいね機能テスト用の商品';
        $item->img_url      = 'items/like-test.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $this->assertEquals(0, Like::count());

        $this->actingAs($user)
            ->post("/item/like/{$item->id}");

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $detail = $this->actingAs($user)
            ->get(route('items.detail', ['item' => $item->id]));

        $detail->assertStatus(200);
        $detail->assertSee('<span class="like-count">1</span>', false);
        $detail->assertSee('class="item__like-btn liked"', false);
    }

    public function test_いいね追加後はアイコンの見た目が変化する()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '中古';
        $condition->save();

        $item = new Item();
        $item->name         = 'アイコン変化テスト商品';
        $item->price        = 2000;
        $item->description  = 'アイコンテスト';
        $item->img_url      = 'items/icon-test.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $before = $this->actingAs($user)
            ->get(route('items.detail', ['item' => $item->id]));

        $before->assertStatus(200);
        $before->assertSee('class="item__like-btn "', false);
        $before->assertDontSee('class="item__like-btn liked"', false);

        $this->actingAs($user)->post("/item/like/{$item->id}");

        $after = $this->actingAs($user)
            ->get(route('items.detail', ['item' => $item->id]));

        $after->assertStatus(200);
        $after->assertSee('class="item__like-btn liked"', false);
    }

    public function test_再度いいね押下でいいね解除されカウントが減る()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '美品';
        $condition->save();

        $item = new Item();
        $item->name         = 'いいね解除テスト商品';
        $item->price        = 3000;
        $item->description  = 'いいね解除テスト';
        $item->img_url      = 'items/unlike-test.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $like = new Like();
        $like->user_id = $user->id;
        $like->item_id = $item->id;
        $like->save();

        $this->assertEquals(1, Like::count());

        $this->actingAs($user)->post("/item/unlike/{$item->id}");

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $detail = $this->actingAs($user)
            ->get(route('items.detail', ['item' => $item->id]));

        $detail->assertStatus(200);
        $detail->assertSee('<span class="like-count">0</span>', false);
        $detail->assertSee('class="item__like-btn "', false);
        $detail->assertDontSee('class="item__like-btn liked"', false);
    }
}
