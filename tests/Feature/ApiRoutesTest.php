<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Board;
use App\Models\Status;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_crud_projects()
    {
        // Create
        $create = $this->postJson('/api/projects', [
            'name' => 'My Project',
            'user_id' => $this->user->id
        ]);

        $create->assertStatus(201);

        $project = Project::latest()->first();
        $this->assertEquals('My Project', $project->name);

        // Read
        $this->get('/api/projects')->assertStatus(200)->assertSee('My Project');

        // Update
        $this->put("/api/projects/{$project->id}", [
            'name' => 'Updated Project'
        ])->assertStatus(200);

        $this->assertEquals('Updated Project', Project::find($project->id)->name);

        // Delete
        $this->delete("/api/projects/{$project->id}")->assertStatus(200);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_can_crud_boards()
    {
        $project = Project::create(['name' => 'Project', 'user_id' => $this->user->id]);

        $create = $this->postJson('/api/boards', [
            'name' => 'Board 1',
            'project_id' => $project->id
        ]);

        $create->assertStatus(201);

        $board = Board::latest()->first();
        $this->assertEquals('Board 1', $board->name);

        $this->get('/api/boards')->assertStatus(200)->assertSee('Board 1');

        $this->put("/api/boards/{$board->id}", [
            'name' => 'Updated Board'
        ])->assertStatus(200);

        $this->assertEquals('Updated Board', Board::find($board->id)->name);

        $this->delete("/api/boards/{$board->id}")->assertStatus(200);
        $this->assertDatabaseMissing('boards', ['id' => $board->id]);
    }

    public function test_can_crud_statuses()
    {
        $project = Project::create(['name' => 'Project', 'user_id' => $this->user->id]);
        $board = Board::create(['name' => 'Board', 'project_id' => $project->id]);

        $create = $this->postJson('/api/statuses', [
            'name' => 'To Do',
            'board_id' => $board->id
        ]);

        $create->assertStatus(201);

        $status = Status::latest()->first();
        $this->assertEquals('To Do', $status->name);

        $this->get('/api/statuses')->assertStatus(200)->assertSee('To Do');

        $this->put("/api/statuses/{$status->id}", [
            'name' => 'In Progress'
        ])->assertStatus(200);

        $this->assertEquals('In Progress', Status::find($status->id)->name);

        $this->delete("/api/statuses/{$status->id}")->assertStatus(200);
        $this->assertDatabaseMissing('statuses', ['id' => $status->id]);
    }

    public function test_can_crud_cards()
    {
        $project = Project::create(['name' => 'Project', 'user_id' => $this->user->id]);
        $board = Board::create(['name' => 'Board', 'project_id' => $project->id]);
        $status = Status::create(['name' => 'To Do', 'board_id' => $board->id]);

        $create = $this->postJson('/api/cards', [
            'title' => 'Card 1',
            'description' => 'Test description',
            'status_id' => $status->id
        ]);

        $create->assertStatus(201);

        $card = Card::latest()->first();
        $this->assertEquals('Card 1', $card->title);

        $this->get('/api/cards')->assertStatus(200)->assertSee('Card 1');

        $this->put("/api/cards/{$card->id}", [
            'title' => 'Updated Card',
            'description' => 'Updated description'
        ])->assertStatus(200);

        $this->assertEquals('Updated Card', Card::find($card->id)->title);

        $this->delete("/api/cards/{$card->id}")->assertStatus(200);
        $this->assertDatabaseMissing('cards', ['id' => $card->id]);
    }
}
