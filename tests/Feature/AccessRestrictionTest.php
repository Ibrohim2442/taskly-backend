<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Board;
use App\Models\Status;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access protected API routes', function () {
    $owner = \App\Models\User::factory()->create();

    $project = \App\Models\Project::factory()
        ->for($owner)
        ->create(['name' => 'Guest Project']);

    $board = \App\Models\Board::factory()
        ->for($project)
        ->create(['name' => 'Board 1']);

    $status = \App\Models\Status::factory()
        ->for($board)
        ->create(['name' => 'Status 1']);

    $card = \App\Models\Card::factory()
        ->for($status)
        ->create(['title' => 'Card 1']);

    $this->getJson("/api/projects")->assertUnauthorized();
    $this->getJson("/api/projects/{$project->id}")->assertUnauthorized();
    $this->postJson("/api/projects", [])->assertUnauthorized();
    $this->patchJson("/api/projects/{$project->id}", [])->assertUnauthorized();
    $this->deleteJson("/api/projects/{$project->id}")->assertUnauthorized();

    $this->getJson("/api/boards/{$board->id}")->assertUnauthorized();
    $this->postJson("/api/boards", [])->assertUnauthorized();

    $this->getJson("/api/statuses/{$status->id}")->assertUnauthorized();
    $this->postJson("/api/statuses", [])->assertUnauthorized();

    $this->getJson("/api/cards/{$card->id}")->assertUnauthorized();
    $this->postJson("/api/cards", [])->assertUnauthorized();
});

test('user cannot access resources owned by another user', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $project = Project::factory()->for($owner)->create(['name' => 'Secret Project']);
    $board = Board::factory()->for($project)->create(['name' => 'Hidden Board']);
    $status = Status::factory()->for($board)->create(['name' => 'Private Status']);
    $card = Card::factory()->for($status)->create(['title' => 'Secret Card']);

    $this->actingAs($intruder);

    $this->getJson("/api/projects/{$project->id}")->assertForbidden();
    $this->patchJson("/api/projects/{$project->id}", ['name' => 'Hack'])->assertForbidden();
    $this->deleteJson("/api/projects/{$project->id}")->assertForbidden();

    $this->getJson("/api/boards/{$board->id}")->assertForbidden();
    $this->patchJson("/api/boards/{$board->id}", ['name' => 'Nope'])->assertForbidden();
    $this->deleteJson("/api/boards/{$board->id}")->assertForbidden();

    $this->getJson("/api/statuses/{$status->id}")->assertForbidden();
    $this->patchJson("/api/statuses/{$status->id}", ['name' => 'No'])->assertForbidden();
    $this->deleteJson("/api/statuses/{$status->id}")->assertForbidden();

    $this->getJson("/api/cards/{$card->id}")->assertForbidden();
    $this->patchJson("/api/cards/{$card->id}", ['title' => 'Stolen'])->assertForbidden();
    $this->deleteJson("/api/cards/{$card->id}")->assertForbidden();
});
