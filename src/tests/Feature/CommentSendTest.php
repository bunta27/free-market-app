<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentSendTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン済みユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $item = new Item();
        $item->name         = 'コメント用商品';
        $item->price        = 1000;
        $item->description  = 'コメントテスト用の商品';
        $item->img_url      = 'items/comment-test.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $this->assertEquals(0, Comment::count());

        $response = $this->actingAs($user)->post(
            route('comments.create', ['item_id' => $item->id]),
            ['comment' => 'とても良さそうですね！']
        );

        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'とても良さそうですね！',
        ]);

        $this->assertEquals(1, Comment::count());

        $detail = $this->actingAs($user)
            ->get(route('items.detail', ['item' => $item->id]));

        $detail->assertStatus(200);
        $detail->assertSee('とても良さそうですね！');
        $detail->assertSee('コメント(1)');
    }

    public function test_未ログインユーザーはコメントを送信できない()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '中古';
        $condition->save();

        $item = new Item();
        $item->name         = 'ゲストコメント商品';
        $item->price        = 2000;
        $item->description  = 'ゲストテスト用';
        $item->img_url      = 'items/guest-comment.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $this->assertEquals(0, Comment::count());

        $response = $this->post(
            route('comments.create', ['item_id' => $item->id]),
            ['comment' => 'ゲストからのコメント']
        );

        $response->assertRedirect('/login');

        $this->assertEquals(0, Comment::count());
    }

    public function test_コメント未入力の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '美品';
        $condition->save();

        $item = new Item();
        $item->name         = 'バリデーション商品1';
        $item->price        = 3000;
        $item->description  = 'バリデーションテスト1';
        $item->img_url      = 'items/validation1.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $response = $this->actingAs($user)->from(
            route('items.detail', ['item' => $item->id])
        )->post(
            route('comments.create', ['item_id' => $item->id]),
            ['comment' => '']
        );

        $response->assertStatus(302);
        $response->assertRedirect(route('items.detail', ['item' => $item->id]));

        $response->assertSessionHasErrors('comment');

        $this->assertEquals(0, Comment::count());
    }

    public function test_コメントが255文字以上の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '傷あり';
        $condition->save();

        $item = new Item();
        $item->name         = 'バリデーション商品2';
        $item->price        = 4000;
        $item->description  = 'バリデーションテスト2';
        $item->img_url      = 'items/validation2.jpg';
        $item->user_id      = $user->id;
        $item->condition_id = $condition->id;
        $item->save();

        $tooLongComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->from(
            route('items.detail', ['item' => $item->id])
        )->post(
            route('comments.create', ['item_id' => $item->id]),
            ['comment' => $tooLongComment]
        );

        $response->assertStatus(302);
        $response->assertRedirect(route('items.detail', ['item' => $item->id]));
        $response->assertSessionHasErrors('comment');

        $this->assertEquals(0, Comment::count());
    }
}
