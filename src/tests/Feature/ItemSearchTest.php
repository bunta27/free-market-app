<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品名で部分一致検索ができる()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $target = new Item();
        $target->name         = 'PHP入門本';
        $target->price        = 2000;
        $target->description  = 'PHP の基礎を学べる本';
        $target->img_url      = 'items/php-book.jpg';
        $target->user_id      = $user->id;
        $target->condition_id = $condition->id;
        $target->save();

        $other = new Item();
        $other->name         = 'JavaScript完全ガイド';
        $other->price        = 2500;
        $other->description  = 'JS の本';
        $other->img_url      = 'items/js-book.jpg';
        $other->user_id      = $user->id;
        $other->condition_id = $condition->id;
        $other->save();

        $response = $this->actingAs($user)->get(route('items.search', ['query' => 'PHP']));

        $response->assertStatus(200);

        $response->assertSee('PHP入門本');

        $response->assertDontSee('JavaScript完全ガイド');
    }

    public function test_検索キーワードがマイリストでも保持される()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '中古';
        $condition->save();

        $item = new Item();
        $item->name         = 'テスト商品';
        $item->price        = 3000;
        $item->description  = 'テスト用の商品';
        $item->img_url      = 'items/test-item.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $like = new Like();
        $like->user_id = $user->id;
        $like->item_id = $item->id;
        $like->save();

        $responseSearch = $this->actingAs($user)
            ->get(route('items.search', ['query' => 'テスト']));

        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('テスト');

        $responseMylist = $this->actingAs($user)
            ->get('/?page=mylist&query=テスト');

        $responseMylist->assertStatus(200);

        $responseMylist->assertSee('テスト');
    }
}
