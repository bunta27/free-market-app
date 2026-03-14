<?php

namespace Tests\Feature;

use App\Mail\TradeCompletedMail;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\TradeMessageRead;
use App\Models\TradeReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TradeTest extends TestCase
{
    use RefreshDatabase;

    private function createTradeData(): array
    {
        $seller = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $other = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $condition = Condition::create([
            'condition' => '良好',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'brand' => 'テストブランド',
            'description' => 'テスト説明',
            'img_url' => 'items/test.jpg',
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
        ]);

        $trade = Trade::create([
            'item_id' => $item->id,
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'ongoing',
        ]);

        return compact('seller', 'buyer', 'other', 'condition', 'item', 'trade');
    }

    public function test_取引当事者は取引画面を開ける()
    {
        $data = $this->createTradeData();

        $response = $this->actingAs($data['buyer'])
            ->get(route('trades.show', $data['trade']));

        $response->assertStatus(200);
    }

    public function test_第三者は取引画面を開けない()
    {
        $data = $this->createTradeData();

        $response = $this->actingAs($data['other'])
            ->get(route('trades.show', $data['trade']));

        $response->assertStatus(403);
    }

    public function test_当事者はメッセージ送信できる()
    {
        $data = $this->createTradeData();

        $response = $this->actingAs($data['buyer'])
            ->post(route('trade.messages.store', $data['trade']), [
                'message' => '購入しました。よろしくお願いします。',
            ]);

        $response->assertRedirect(route('trades.show', $data['trade']));

        $this->assertDatabaseHas('trade_messages', [
            'trade_id' => $data['trade']->id,
            'user_id' => $data['buyer']->id,
            'message' => '購入しました。よろしくお願いします。',
        ]);
    }

    public function test_第三者はメッセージ送信できない()
    {
        $data = $this->createTradeData();

        $response = $this->actingAs($data['other'])
            ->post(route('trade.messages.store', $data['trade']), [
                'message' => '不正投稿',
            ]);

        $response->assertStatus(403);
    }

    public function test_自分のメッセージは編集できる()
    {
        $data = $this->createTradeData();

        $message = TradeMessage::create([
            'trade_id' => $data['trade']->id,
            'user_id' => $data['buyer']->id,
            'message' => '編集前メッセージ',
        ]);

        $response = $this->actingAs($data['buyer'])
            ->put(route('trade.messages.update', $message), [
                'message' => '編集後メッセージ',
            ]);

        $response->assertRedirect(route('trades.show', $data['trade']));

        $this->assertDatabaseHas('trade_messages', [
            'id' => $message->id,
            'message' => '編集後メッセージ',
        ]);
    }

    public function test_他人のメッセージは編集できない()
    {
        $data = $this->createTradeData();

        $message = TradeMessage::create([
            'trade_id' => $data['trade']->id,
            'user_id' => $data['buyer']->id,
            'message' => '購入者メッセージ',
        ]);

        $response = $this->actingAs($data['seller'])
            ->put(route('trade.messages.update', $message), [
                'message' => '勝手に編集',
            ]);

        $response->assertStatus(403);
    }

    public function test_自分のメッセージは削除できる()
    {
        $data = $this->createTradeData();

        $message = TradeMessage::create([
            'trade_id' => $data['trade']->id,
            'user_id' => $data['buyer']->id,
            'message' => '削除対象',
        ]);

        $response = $this->actingAs($data['buyer'])
            ->delete(route('trade.messages.destroy', $message));

        $response->assertRedirect(route('trades.show', $data['trade']));

        $this->assertSoftDeleted('trade_messages', [
            'id' => $message->id,
        ]);
    }

    public function test_メッセージが空なら送信できない()
    {
        $data = $this->createTradeData();

        $response = $this->actingAs($data['buyer'])
            ->post(route('trade.messages.store', $data['trade']), [
                'message' => '',
            ]);

        $response->assertSessionHasErrors(['message']);

        $this->assertDatabaseCount('trade_messages', 0);
    }

    public function test_メッセージが空なら画像があっても更新できない()
    {
        Storage::fake('public');

        $data = $this->createTradeData();

        $message = TradeMessage::create([
            'trade_id' => $data['trade']->id,
            'user_id' => $data['buyer']->id,
            'message' => '更新前メッセージ',
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($tempPath, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aX8kAAAAASUVORK5CYII='
        ));

        $image = new \Illuminate\Http\UploadedFile(
            $tempPath,
            'sample.png',
            'image/png',
            null,
            true
        );

        $response = $this->actingAs($data['buyer'])
            ->from(route('trade.messages.edit', $message))
            ->put(route('trade.messages.update', $message), [
                'message' => '',
                'image' => $image,
            ]);

        $response->assertRedirect(route('trade.messages.edit', $message));
        $response->assertSessionHasErrors(['message']);

        $this->assertDatabaseHas('trade_messages', [
            'id' => $message->id,
            'message' => '更新前メッセージ',
        ]);
    }

    public function test_評価が未選択なら送信できない()
    {
        $data = $this->createTradeData();

        $data['trade']->update([
            'status' => 'buyer_completed',
            'buyer_completed_at' => now(),
        ]);

        $response = $this->actingAs($data['buyer'])
            ->post(route('trade.reviews.store', $data['trade']), [
                'comment' => 'コメントだけ送信',
            ]);

        $response->assertSessionHasErrors(['rating']);

        $this->assertDatabaseMissing('trade_reviews', [
            'trade_id' => $data['trade']->id,
            'reviewer_id' => $data['buyer']->id,
        ]);
    }

    public function test_購入者は取引完了できる()
    {
        Mail::fake();

        $data = $this->createTradeData();

        $response = $this->actingAs($data['buyer'])
            ->post(route('trades.complete', $data['trade']));

        $response->assertRedirect(route('trades.show', $data['trade']));

        $this->assertDatabaseHas('trades', [
            'id' => $data['trade']->id,
            'status' => 'buyer_completed',
        ]);

        Mail::assertSent(TradeCompletedMail::class);
    }

    public function test_出品者は取引完了できない()
    {
        Mail::fake();

        $data = $this->createTradeData();

        $response = $this->actingAs($data['seller'])
            ->post(route('trades.complete', $data['trade']));

        $response->assertRedirect(route('trades.show', $data['trade']));

        $this->assertDatabaseHas('trades', [
            'id' => $data['trade']->id,
            'status' => 'ongoing',
        ]);

        Mail::assertNothingSent();
    }

    public function test_取引完了後は評価できる()
    {
        $data = $this->createTradeData();

        $data['trade']->update([
            'status' => 'buyer_completed',
            'buyer_completed_at' => now(),
        ]);

        $response = $this->actingAs($data['buyer'])
            ->post(route('trade.reviews.store', $data['trade']), [
                'rating' => 5,
                'comment' => 'とても良い取引でした',
            ]);

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('trade_reviews', [
            'trade_id' => $data['trade']->id,
            'reviewer_id' => $data['buyer']->id,
            'reviewee_id' => $data['seller']->id,
            'rating' => 5,
        ]);
    }

    public function test_取引完了前は評価できない()
    {
        $data = $this->createTradeData();

        $response = $this->actingAs($data['buyer'])
            ->post(route('trade.reviews.store', $data['trade']), [
                'rating' => 5,
                'comment' => 'まだ評価できないはず',
            ]);

        $response->assertRedirect(route('trades.show', $data['trade']));

        $this->assertDatabaseMissing('trade_reviews', [
            'trade_id' => $data['trade']->id,
            'reviewer_id' => $data['buyer']->id,
        ]);
    }

    public function test_同じ取引で同じ人は二重評価できない()
    {
        $data = $this->createTradeData();

        $data['trade']->update([
            'status' => 'buyer_completed',
            'buyer_completed_at' => now(),
        ]);

        TradeReview::create([
            'trade_id' => $data['trade']->id,
            'reviewer_id' => $data['buyer']->id,
            'reviewee_id' => $data['seller']->id,
            'rating' => 4,
            'comment' => '1回目',
        ]);

        $response = $this->actingAs($data['buyer'])
            ->post(route('trade.reviews.store', $data['trade']), [
                'rating' => 5,
                'comment' => '2回目',
            ]);

        $response->assertRedirect(route('trades.show', $data['trade']));

        $this->assertEquals(
            1,
            TradeReview::where('trade_id', $data['trade']->id)
                ->where('reviewer_id', $data['buyer']->id)
                ->count()
        );
    }

    public function test_両者の評価が揃うと取引完了になる()
    {
        $data = $this->createTradeData();

        $data['trade']->update([
            'status' => 'buyer_completed',
            'buyer_completed_at' => now(),
        ]);

        TradeReview::create([
            'trade_id' => $data['trade']->id,
            'reviewer_id' => $data['buyer']->id,
            'reviewee_id' => $data['seller']->id,
            'rating' => 5,
            'comment' => '購入者評価',
        ]);

        $response = $this->actingAs($data['seller'])
            ->post(route('trade.reviews.store', $data['trade']), [
                'rating' => 4,
                'comment' => '出品者評価',
            ]);

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('trades', [
            'id' => $data['trade']->id,
            'status' => 'completed',
        ]);
    }

    public function test_マイページの取引中タブで取引中商品が表示される()
    {
        $data = $this->createTradeData();

        $response = $this->actingAs($data['buyer'])
            ->get(route('mypage', ['page' => 'trade']));

        $response->assertStatus(200);
        $response->assertSee('取引中の商品');
        $response->assertSee($data['item']->name);
    }

    public function test_未読メッセージがある取引では未読件数が表示される()
    {
        $data = $this->createTradeData();

        TradeMessage::create([
            'trade_id' => $data['trade']->id,
            'user_id' => $data['seller']->id,
            'message' => '未読メッセージ',
        ]);

        $response = $this->actingAs($data['buyer'])
            ->get(route('mypage', ['page' => 'trade']));

        $response->assertStatus(200);
        $response->assertSee((string) 1);
    }

    public function test_取引画面を開くと相手のメッセージが既読になる()
    {
        $data = $this->createTradeData();

        $message = TradeMessage::create([
            'trade_id' => $data['trade']->id,
            'user_id' => $data['seller']->id,
            'message' => '未読メッセージ',
        ]);

        $this->actingAs($data['buyer'])
            ->get(route('trades.show', $data['trade']))
            ->assertStatus(200);

        $this->assertDatabaseHas('trade_message_reads', [
            'trade_message_id' => $message->id,
            'user_id' => $data['buyer']->id,
        ]);
    }

    public function test_既読後は未読件数が表示されない()
    {
        $data = $this->createTradeData();

        $message = TradeMessage::create([
            'trade_id' => $data['trade']->id,
            'user_id' => $data['seller']->id,
            'message' => '未読メッセージ',
        ]);

        TradeMessageRead::create([
            'trade_message_id' => $message->id,
            'user_id' => $data['buyer']->id,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($data['buyer'])
            ->get(route('mypage', ['page' => 'trade']));

        $response->assertStatus(200);
        $response->assertDontSee('item__badge', false);
    }
}