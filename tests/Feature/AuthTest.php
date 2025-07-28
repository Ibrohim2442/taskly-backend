<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('user can register', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token'
        ]);
});

test('user can login and receive token', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token'
        ]);
});

test('authenticated user can get profile info via /me', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);
});

test('user can logout and invalidate token', function () {
    $user = User::factory()->create();

    $token = $user->createToken('logout-test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'You are logged out.',
        ]);
});
