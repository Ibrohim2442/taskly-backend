<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CardController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Card::class, 'card');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = Card::whereHas('status.board.project', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => $cards
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
        ]);

        $card = Card::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $card
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        return response()->json([
            'status' => 'success',
            'data' => $card
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'status_id' => 'sometimes|exists:statuses,id',
        ]);

        $card->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $card
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        $card->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Card deleted successfully'
        ]);
    }
}
