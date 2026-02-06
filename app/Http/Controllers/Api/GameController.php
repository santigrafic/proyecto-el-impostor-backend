<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GameService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Estado de la partida (Game Page)
     */
    public function state(string $roomId)
    {
        try {
            return response()->json(
                $this->gameService->getGameState($roomId)
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Información privada del jugador (palabra / rol)
     */
    public function me(string $roomId, Request $request)
    {
        $data = $request->validate([
            'playerId' => 'required|string'
        ]);

        try {
            return response()->json(
                $this->gameService->getPlayerGameInfo(
                    $roomId,
                    $data['playerId']
                )
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Jugar palabra
     */
    public function playWord(string $roomId, Request $request)
    {
        $data = $request->validate([
            'playerId' => 'required|string',
            'word' => 'required|string|max:100'
        ]);

        try {
            return response()->json(
                $this->gameService->playWord(
                    $roomId,
                    $data['playerId'],
                    $data['word']
                )
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function vote(string $roomId, Request $request)
    {
        $data = $request->validate([
            'playerId' => 'required|string',
            'votedPlayerId' => 'required|string'
        ]);

        return response()->json(
            $this->gameService->vote(
                $roomId,
                $data['playerId'],
                $data['votedPlayerId']
            )
        );
    }
}
