<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserAuthAndCrudTest extends TestCase
{
    use RefreshDatabase;
    public function test_login_success_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'testowner',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testowner',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'username', 'role'],
            ]);

        $user->delete();
    }

    public function test_login_failed_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'username' => 'nonexistent',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Username atau password yang Anda masukkan salah.',
            ]);
    }

    public function test_karyawan_cannot_access_user_crud(): void
    {
        $karyawan = User::factory()->create([
            'username' => 'testkaryawan',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
        ]);

        $response = $this->actingAs($karyawan, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Akses ditolak. Hanya owner yang diizinkan mengelola akun pengguna.',
            ]);

        $karyawan->delete();
    }

    public function test_owner_can_crud_users(): void
    {
        $owner = User::factory()->create([
            'username' => 'superowner',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        // 1. Owner can list users
        $listResponse = $this->actingAs($owner, 'sanctum')
            ->getJson('/api/users');
        $listResponse->assertStatus(200);

        // 2. Owner can create user
        $createResponse = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/users', [
                'username' => 'newstaff',
                'password' => 'staff123',
                'role' => 'karyawan',
            ]);
        $createResponse->assertStatus(201)
            ->assertJsonPath('data.username', 'newstaff')
            ->assertJsonPath('data.role', 'karyawan');

        $createdId = $createResponse->json('data.id');

        // 3. Owner can view detail
        $showResponse = $this->actingAs($owner, 'sanctum')
            ->getJson("/api/users/{$createdId}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.id', $createdId);

        // 4. Owner can update user
        $updateResponse = $this->actingAs($owner, 'sanctum')
            ->putJson("/api/users/{$createdId}", [
                'username' => 'updatedstaff',
                'role' => 'karyawan',
            ]);
        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.username', 'updatedstaff');

        // 5. Owner cannot delete their own account
        $selfDeleteResponse = $this->actingAs($owner, 'sanctum')
            ->deleteJson("/api/users/{$owner->id}");
        $selfDeleteResponse->assertStatus(400)
            ->assertJson([
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);

        // 6. Owner can delete another user
        $deleteResponse = $this->actingAs($owner, 'sanctum')
            ->deleteJson("/api/users/{$createdId}");
        $deleteResponse->assertStatus(200)
            ->assertJson([
                'message' => 'Akun pengguna berhasil dihapus.',
            ]);

        $owner->delete();
    }
}
