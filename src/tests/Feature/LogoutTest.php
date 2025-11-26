<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン中ユーザーはログアウトできる()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();

        $response->assertRedirect('/');
    }

    public function test_未ログイン状態でログアウトを叩いてもエラーにならない()
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
