<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class LogoutCsrfTest extends TestCase
{
    use DatabaseTransactions;

    public function test_logout_succeeds_without_csrf_token(): void
    {
        $plan = Plan::first() ?: Plan::create([
            'uuid' => (string) Str::uuid(),
            'nama_plan' => 'Plan Test',
        ]);

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Logout Test User',
            'username' => 'logout_test_' . Str::random(6),
            'email' => 'logout_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'qc',
            'id_role' => 3,
            'id_plan' => $plan->id,
        ]);

        // Simulasikan POST /logout tanpa menyertakan CSRF token sama sekali
        $response = $this->actingAs($user)->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class)
            ->post('/logout');

        // Harus berhasil redirect ke /login tanpa error 419
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_logout_succeeds_with_expired_or_invalid_csrf_token(): void
    {
        $plan = Plan::first() ?: Plan::create([
            'uuid' => (string) Str::uuid(),
            'nama_plan' => 'Plan Test',
        ]);

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Logout Test User 2',
            'username' => 'logout_test_' . Str::random(6),
            'email' => 'logout_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'produksi',
            'id_role' => 2,
            'id_plan' => $plan->id,
        ]);

        // Kirim token CSRF yang palsu/kedaluwarsa
        $response = $this->actingAs($user)->post('/logout', [
            '_token' => 'invalid_or_expired_token_value',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}

