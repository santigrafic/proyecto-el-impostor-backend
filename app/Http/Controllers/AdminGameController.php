<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class AdminGameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::with('users')->orderBy('created_at')->paginate(10);
        return view('admin.games.index', compact('games'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $game = Game::findOrFail($id);
        return view('admin.games.show', compact('game'));
    }

    /**
     * The rest is intentionally disabled (read-only section)
     */
    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        abort(404);
    }

    public function destroy(string $id)
    {
        abort(404);
    }
}