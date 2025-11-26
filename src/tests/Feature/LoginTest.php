<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_メール未入力の場合はエラーが表示される()
    {
        $response = $this->post('/login', [
            'email'    => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_パスワード未入力の場合はエラーが表示される()
    {
        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);

        $this->assertGuest();
    }

    public function test_誤った情報の場合はログインできない()
    {
        $user = User::factory()->create([
            'email'    => 'real@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->from('/login')
                        ->post('/login', [
                            'email'    => 'fake@example.com',
                            'password' => 'wrong-password',
                        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_正しい情報ならログイン成功してマイページにリダイレクトされる()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect('/');
    }
}
