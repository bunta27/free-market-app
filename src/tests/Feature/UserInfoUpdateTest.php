<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInfoUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィール編集画面でユーザー情報が初期値として表示される()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);

        $profile = new Profile();
        $profile->user_id  = $user->id;
        $profile->img_url  = 'profiles/test-icon.png';
        $profile->postcode = '150-0002';
        $profile->address  = 'テスト県テスト市2-2-2';
        $profile->building = 'テストタワー202';
        $profile->save();

        $response = $this->actingAs($user)
            ->get('/mypage/profile');

        $response->assertStatus(200);

        $response->assertSee('profiles/test-icon.png');

        $response->assertSee('value="テストユーザー"', false);

        $response->assertSee('value="150-0002"', false);

        $response->assertSee('value="テスト県テスト市2-2-2"', false);

        $response->assertSee('value="テストタワー202"', false);
    }
}
