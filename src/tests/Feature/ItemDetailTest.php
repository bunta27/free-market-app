<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use App\Models\CategoryItem;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品詳細ページで必要な情報が表示される()
    {
        $seller = User::factory()->create([
            'name' => '出品者ユーザー',
        ]);

        $viewer = User::factory()->create([
            'name' => '閲覧ユーザー',
        ]);

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $item = new Item();
        $item->name         = 'テスト商品詳細';
        $item->price        = 12345;
        $item->description  = 'これは詳細ページのテスト商品です。';
        $item->img_url      = 'items/detail-test.jpg';
        $item->user_id      = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        $category1 = new Category();
        $category1->category = '家電';
        $category1->save();

        $category2 = new Category();
        $category2->category = '生活雑貨';
        $category2->save();

        $pivot1 = new CategoryItem();
        $pivot1->item_id     = $item->id;
        $pivot1->category_id = $category1->id;
        $pivot1->save();

        $pivot2 = new CategoryItem();
        $pivot2->item_id     = $item->id;
        $pivot2->category_id = $category2->id;
        $pivot2->save();

        $like = new Like();
        $like->user_id = $viewer->id;
        $like->item_id = $item->id;
        $like->save();

        $commentUser = User::factory()->create([
            'name' => 'コメントユーザー',
        ]);

        $comment = new Comment();
        $comment->user_id = $commentUser->id;
        $comment->item_id = $item->id;
        $comment->comment = 'とても良さそうな商品ですね！';
        $comment->save();

        $response = $this->actingAs($viewer)
                        ->get(route('items.detail', ['item' => $item->id]));

        $response->assertStatus(200);

        $response->assertSee('テスト商品詳細');
        $response->assertSee('12,345');
        $response->assertSee('これは詳細ページのテスト商品です。');

        $response->assertSee('新品');

        $response->assertSee('家電');
        $response->assertSee('生活雑貨');

        $response->assertSee('コメントユーザー');
        $response->assertSee('とても良さそうな商品ですね！');
    }

    public function test_複数カテゴリが商品詳細ページに表示される()
    {
        $seller = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '中古';
        $condition->save();

        $item = new Item();
        $item->name         = 'カテゴリテスト商品';
        $item->price        = 5000;
        $item->description  = 'カテゴリ複数テスト用';
        $item->img_url      = 'items/category-test.jpg';
        $item->user_id      = $seller->id;
        $item->condition_id = $condition->id;
        $item->save();

        $categoryA = new Category();
        $categoryA->category = '本';
        $categoryA->save();

        $categoryB = new Category();
        $categoryB->category = '勉強';
        $categoryB->save();

        $pivotA = new CategoryItem();
        $pivotA->item_id     = $item->id;
        $pivotA->category_id = $categoryA->id;
        $pivotA->save();

        $pivotB = new CategoryItem();
        $pivotB->item_id     = $item->id;
        $pivotB->category_id = $categoryB->id;
        $pivotB->save();

        $response = $this->get(route('items.detail', ['item' => $item->id]));

        $response->assertStatus(200);

        $response->assertSee('本');
        $response->assertSee('勉強');
    }
}
