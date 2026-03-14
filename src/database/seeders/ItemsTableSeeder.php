<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Category;
use App\Models\CategoryItem;

class ItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'seller1@example.com')->firstOrFail();
        $user2 = User::where('email', 'seller2@example.com')->firstOrFail();

        $rows = [
            ['code' => 'C001', 'name' => '腕時計',         'price' => 15000, 'brand' => 'Rolax',     'desc' => 'スタイリッシュなデザインのメンズ腕時計',   'img' => 'Armani+Mens+Clock.jpg',              'cond' => '良好'],
            ['code' => 'C002', 'name' => 'HDD',           'price' => 5000,  'brand' => '西芝',       'desc' => '高速で信頼性の高いハードディスク',         'img' => 'HDD+Hard+Disk.jpg',                  'cond' => '目立った傷や汚れなし'],
            ['code' => 'C003', 'name' => '玉ねぎ3束',      'price' => 300,   'brand' => 'なし',       'desc' => '新鮮な玉ねぎ3束のセット',                 'img' => 'iLoveIMG+d.jpg',                     'cond' => 'やや傷や汚れあり'],
            ['code' => 'C004', 'name' => '革靴',          'price' => 4000,  'brand' => 'なし',       'desc' => 'クラシックなデザインの革靴',             'img' => 'Leather+Shoes+Product+Photo.jpg',   'cond' => '状態が悪い'],
            ['code' => 'C005', 'name' => 'ノートPC',      'price' => 45000, 'brand' => 'なし',       'desc' => '高性能なノートパソコン',                 'img' => 'Living+Room+Laptop.jpg',            'cond' => '良好'],
            ['code' => 'C006', 'name' => 'マイク',        'price' => 8000,  'brand' => 'なし',       'desc' => '高音質のレコーディング用マイク',         'img' => 'Music+Mic+4632231.jpg',             'cond' => '目立った傷や汚れなし'],
            ['code' => 'C007', 'name' => 'ショルダーバッグ', 'price' => 3500,  'brand' => 'なし',       'desc' => 'おしゃれなショルダーバッグ',             'img' => 'Purse+fashion+pocket.jpg',          'cond' => 'やや傷や汚れあり'],
            ['code' => 'C008', 'name' => 'タンブラー',     'price' => 500,   'brand' => 'なし',       'desc' => '使いやすいタンブラー',                   'img' => 'Tumbler+souvenir.jpg',              'cond' => '状態が悪い'],
            ['code' => 'C009', 'name' => 'コーヒーミル',   'price' => 4000,  'brand' => 'Starbacks',  'desc' => '手動のコーヒーミル',                     'img' => 'Waitress+with+Coffee+Grinder.jpg',  'cond' => '良好'],
            ['code' => 'C010', 'name' => 'メイクセット',   'price' => 2500,  'brand' => 'なし',       'desc' => '便利なメイクアップセット',               'img' => '外出メイクアップセット.jpg',            'cond' => '目立った傷や汚れなし'],
        ];

        foreach ($rows as $index => $r) {
            $conditionId = Condition::where('condition', $r['cond'])->value('id')
                ?? Condition::firstOrFail()->id;

            $userId = $index < 5 ? $user1->id : $user2->id;

            $item = Item::updateOrCreate(
                [
                    'name'    => $r['name'],
                    'user_id' => $userId,
                ],
                [
                    'price'        => $r['price'],
                    'brand'        => $r['brand'],
                    'description'  => $r['desc'],
                    'img_url'      => 'items/' . $r['img'],
                    'condition_id' => $conditionId,
                ]
            );

            $categoryName = match (true) {
                str_contains($r['name'], 'PC') || str_contains($r['name'], 'HDD') || str_contains($r['name'], 'マイク') => '家電',
                str_contains($r['name'], '革靴') || str_contains($r['name'], 'バッグ') || str_contains($r['name'], '腕時計') => 'ファッション',
                str_contains($r['name'], '玉ねぎ') => '食品',
                str_contains($r['name'], 'メイク') => 'コスメ',
                default => '生活雑貨',
            };

            $categoryId = Category::where('category', $categoryName)->value('id');

            if ($categoryId) {
                CategoryItem::firstOrCreate([
                    'item_id'     => $item->id,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }
}