<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Board;
use App\Models\Status;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('project structure and relationships work correctly', function () {
    $user = User::factory()->create();

    $project = Project::create([
        'name' => 'Test Project',
        'description' => 'This is a test',
        'user_id' => $user->id,
    ]);

    $board = Board::create([
        'name' => 'Test Board',
        'project_id' => $project->id,
    ]);

    $status = Status::create([
        'name' => 'To Do',
        'board_id' => $board->id,
    ]);

    $card = Card::create([
        'title' => 'Test Card',
        'description' => 'Some description',
        'status_id' => $status->id,
    ]);

    expect($project->user->id)->toBe($user->id);
    expect($project->boards)->toHaveCount(1);
    expect($board->statuses)->toHaveCount(1);
    expect($status->cards)->toHaveCount(1);
    expect($status->cards->first()->title)->toBe('Test Card');
});
