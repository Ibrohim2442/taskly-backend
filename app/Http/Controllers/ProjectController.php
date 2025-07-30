<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Project::with('user')->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $project = $request->user()->projects()->create($validated);

        return ['project' => $project, 'user' => $project->user];
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return ['project' => $project, 'user' => $project->user];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        $project->update($validated);

        return $project;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return [
            'message' => 'Deleted successfully'
        ];
    }
}
