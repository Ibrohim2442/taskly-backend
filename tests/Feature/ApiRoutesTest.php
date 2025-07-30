<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can crud projects with formatted response', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create
    $res = $this->postJson('/api/projects', [
        'name' => 'Test Project',
        'description' => 'Test Desc'
    ]);

    $res->assertCreated()
        ->assertJsonStructure([
            'status',
            'data' => [
                'project' => ['id', 'name', 'description', 'user_id'],
                'user'
            ]
        ]);

    $projectId = $res->json('data.project.id');

    // Read
    $this->getJson("/api/projects/{$projectId}")
        ->assertOk()
        ->assertJsonPath('data.project.name', 'Test Project');

    // Update
    $this->patchJson("/api/projects/{$projectId}", [
        'name' => 'Updated Project'
    ])->assertOk()
        ->assertJsonPath('data.name', 'Updated Project');

    // Delete
    $this->deleteJson("/api/projects/{$projectId}")
        ->assertOk()
        ->assertJson(['status' => 'success', 'message' => 'Deleted successfully']);
});

test('can crud boards with response wrapper', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $project = $user->projects()->create([
        'name' => 'Parent Project',
    ]);

    // Create
    $res = $this->postJson('/api/boards', [
        'name' => 'Board A',
        'project_id' => $project->id
    ]);

    $res->assertCreated()
        ->assertJsonStructure([
            'status',
            'data' => ['id', 'name', 'project_id']
        ]);

    $boardId = $res->json('data.id');

    // Read
    $this->getJson("/api/boards/{$boardId}")
        ->assertOk()
        ->assertJsonPath('data.name', 'Board A');

    // Update
    $this->patchJson("/api/boards/{$boardId}", [
        'name' => 'Board A+'
    ])->assertOk()
        ->assertJsonPath('data.name', 'Board A+');

    // Delete
    $this->deleteJson("/api/boards/{$boardId}")
        ->assertOk()
        ->assertJson(['status' => 'success', 'message' => 'Board deleted successfully']);
});

test('can crud statuses properly formatted', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $project = $user->projects()->create(['name' => 'Test P']);
    $board = $project->boards()->create(['name' => 'Test B']);

    // Create
    $res = $this->postJson('/api/statuses', [
        'name' => 'To Do',
        'board_id' => $board->id
    ]);

    $res->assertCreated()
        ->assertJsonStructure([
            'status',
            'data' => ['id', 'name', 'board_id']
        ]);

    $statusId = $res->json('data.id');

    // Show
    $this->getJson("/api/statuses/{$statusId}")
        ->assertOk()
        ->assertJsonPath('data.name', 'To Do');

    // Update
    $this->patchJson("/api/statuses/{$statusId}", [
        'name' => 'Done'
    ])->assertOk()
        ->assertJsonPath('data.name', 'Done');

    // Delete
    $this->deleteJson("/api/statuses/{$statusId}")
        ->assertOk()
        ->assertJson(['status' => 'success', 'message' => 'Status deleted successfully']);
});

test('can crud cards with full chain and formatted json', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $project = $user->projects()->create(['name' => 'Proj']);
    $board = $project->boards()->create(['name' => 'Brd']);
    $status = $board->statuses()->create(['name' => 'Stat']);

    // Create
    $res = $this->postJson('/api/cards', [
        'title' => 'Card A',
        'description' => 'Card Description',
        'status_id' => $status->id
    ]);

    $res->assertCreated()
        ->assertJsonStructure([
            'status',
            'data' => ['id', 'title', 'description', 'status_id']
        ]);

    $cardId = $res->json('data.id');

    // Show
    $this->getJson("/api/cards/{$cardId}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Card A');

    // Update
    $this->patchJson("/api/cards/{$cardId}", [
        'title' => 'Card A+'
    ])->assertOk()
        ->assertJsonPath('data.title', 'Card A+');

    // Delete
    $this->deleteJson("/api/cards/{$cardId}")
        ->assertOk()
        ->assertJson(['status' => 'success', 'message' => 'Card deleted successfully']);
});
