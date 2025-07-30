<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BoardController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Board::class, 'board');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boards = Board::whereHas('project', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => $boards
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'project_id' => 'required|exists:projects,id',
        ]);

        $board = Board::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $board
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        return response()->json([
            'status' => 'success',
            'data' => $board
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Board $board)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'project_id' => 'sometimes|exists:projects,id',
        ]);

        $board->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $board
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board)
    {
        $board->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Board deleted successfully'
        ]);
    }
}
