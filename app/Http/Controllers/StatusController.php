<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StatusController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Status::class, 'status');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::whereHas('board.project', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => $statuses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'board_id' => 'required|exists:boards,id',
        ]);

        $status = Status::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $status
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        return response()->json([
            'status' => 'success',
            'data' => $status
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Status $status)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'board_id' => 'sometimes|exists:boards,id',
        ]);

        $status->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $status
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        $status->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Status deleted successfully'
        ]);
    }
}
