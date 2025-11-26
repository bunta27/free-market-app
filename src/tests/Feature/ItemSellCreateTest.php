<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemSellCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_出品商品情報が正しく保存される()
    {
        $user = User::factory()->create();

        $condition = new Condition();
        $condition->condition = '新品';
        $condition->save();

        $category1 = new Category();
        $category1->category = '家電';
        $category1->save();

        $category2 = new Category();
        $category2->category = '生活雑貨';
        $category2->save();

        Storage::fake('public');
        $image = UploadedFile::fake()->create('sell-item.jpg', 100, 'image/jpeg');

        $postData = [
            'name'         => '出品テスト商品',
            'brand'        => 'テストブランド',
            'price'        => 5800,
            'description'  => '出品商品登録テストの説明です。',
            'img_url'      => $image,
            'condition_id' => $condition->id,
            'categories'   => [$category1->id, $category2->id],
        ];

        $response = $this->actingAs($user)
            ->post(route('items.sell.create'), $postData);

        $response->assertStatus(302);

        $this->assertDatabaseHas('items', [
            'name'         => '出品テスト商品',
            'brand'        => 'テストブランド',
            'price'        => 5800,
            'description'  => '出品商品登録テストの説明です。',
            'user_id'      => $user->id,
            'condition_id' => $condition->id,
        ]);

        $item = Item::where('name', '出品テスト商品')->first();
        $this->assertNotNull($item);

        Storage::disk('public')->assertExists($item->img_url);

        $this->assertDatabaseHas('category_items', [
            'item_id'     => $item->id,
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('category_items', [
            'item_id'     => $item->id,
            'category_id' => $category2->id,
        ]);
    }
}
