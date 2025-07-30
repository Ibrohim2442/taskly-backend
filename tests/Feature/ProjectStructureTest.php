<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('project structure and relationships work correctly', function () {
    $user = User::factory()->create();

    $project = $user->projects()->create([
        'name' => 'Test Project',
        'description' => 'This is a test',
    ]);

    $board = $project->boards()->create([
        'name' => 'Test Board',
    ]);

    $status = $board->statuses()->create([
        'name' => 'To Do',
    ]);

    $card = $status->cards()->create([
        'title' => 'Test Card',
        'description' => 'Some description',
    ]);

    expect($project->user->id)->toBe($user->id);
    expect($project->boards)->toHaveCount(1);
    expect($board->statuses)->toHaveCount(1);
    expect($status->cards)->toHaveCount(1);
    expect($status->cards->first()->title)->toBe('Test Card');

});
