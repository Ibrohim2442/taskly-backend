<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can crud projects', function () {
    $user = User::factory()->create();

    // Create
    $response = $this->actingAs($user)->postJson('/api/projects', [
        'name' => 'My First Project',
        'description' => 'Some optional description',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'project' => ['id', 'name', 'description', 'user_id']
        ]);

    $projectId = $response->json('project.id');

    // Read
    $getResponse = $this->getJson("/api/projects/{$projectId}");
    $getResponse->assertStatus(200)
        ->assertJsonPath('project.name', 'My First Project');

    // Update
    $updateResponse = $this->patchJson("/api/projects/{$projectId}", [
        'name' => 'Updated Project',
    ]);
    $updateResponse->assertStatus(200)
        ->assertJsonPath('name', 'Updated Project');

    // Delete
    $deleteResponse = $this->deleteJson("/api/projects/{$projectId}");
    $deleteResponse->assertStatus(200)
        ->assertJson(['message' => 'Deleted successfully']);
});

it('can crud boards', function () {
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);

    $project = $user->projects()->create([
        'name' => 'Project with Board',
        'description' => 'Test project',
    ]);

    // Create board
    $response = $this->postJson('/api/boards', [
        'name' => 'Initial Board',
        'project_id' => $project->id,
    ]);

    $response->assertStatus(201) // Controller 201 emas, 200 qaytaradi
    ->assertJsonStructure(['id', 'name', 'project_id']);

    $boardId = $response->json('id');

    // Read board
    $this->getJson("/api/boards/{$boardId}")
        ->assertStatus(200)
        ->assertJson([
            'id' => $boardId,
            'name' => 'Initial Board',
            'project_id' => $project->id,
        ]);

    // Update board
    $this->putJson("/api/boards/{$boardId}", [
        'name' => 'Updated Board',
    ])->assertStatus(200)
        ->assertJson(['name' => 'Updated Board']);

    // Delete board
    $this->deleteJson("/api/boards/{$boardId}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Deleted successfully']);
});

it('can crud statuses', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Project va Board yaratamiz
    $project = $user->projects()->create([
        'name' => 'Test Project',
        'description' => 'For statuses test',
    ]);

    $board = $project->boards()->create([
        'name' => 'Test Board',
    ]);

    // 1. Create Status
    $response = $this->postJson('/api/statuses', [
        'name' => 'To Do',
        'board_id' => $board->id,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['id', 'name', 'board_id']);

    $statusId = $response->json('id');

    // 2. Update
    $response = $this->patchJson("/api/statuses/{$statusId}", [
        'name' => 'In Progress',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'In Progress']);

    // 3. Show
    $response = $this->getJson("/api/statuses/{$statusId}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $statusId,
            'name' => 'In Progress',
            'board_id' => $board->id,
        ]);

    // 4. Delete
    $response = $this->deleteJson("/api/statuses/{$statusId}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Deleted successfully']);
});

it('can crud cards', function () {
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);

    // Project -> Board -> Status
    $project = $user->projects()->create([
        'name' => 'Project with Cards',
        'description' => 'Card testing project',
    ]);

    $board = $project->boards()->create([
        'name' => 'Board 1',
    ]);

    $status = $board->statuses()->create([
        'name' => 'To Do',
    ]);

    // 1. Create card
    $response = $this->postJson('/api/cards', [
        'title' => 'First Card',
        'description' => 'This is a test card',
        'status_id' => $status->id,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['id', 'title', 'description', 'status_id']);

    $cardId = $response->json('id');

    // 2. Read card
    $this->getJson("/api/cards/{$cardId}")
        ->assertStatus(200)
        ->assertJson([
            'id' => $cardId,
            'title' => 'First Card',
            'status_id' => $status->id,
        ]);

    // 3. Update card
    $this->patchJson("/api/cards/{$cardId}", [
        'title' => 'Updated Card Title',
    ])->assertStatus(200)
        ->assertJsonFragment(['title' => 'Updated Card Title']);

    // 4. Delete card
    $this->deleteJson("/api/cards/{$cardId}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Deleted successfully']);
});
