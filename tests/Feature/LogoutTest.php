<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test_logout_mengarahkan_ke_halaman_login(): void
    {
        Http::fake();

        $user = User::create([
            'name' => 'Logout Tester',
            'username' => 'logout.tester.' . Str::random(5),
            'email' => 'logout.' . Str::random(5) . '@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
