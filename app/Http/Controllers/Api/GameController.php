<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Events\WordPlayed;
use App\Events\TurnChanged;

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
        $result = $this->gameService->playWord(
            $roomId,
            $data['playerId'],
            $data['word']
        );

        // 🔥 IMPORTANTE: usar service en vez de cache directo
        $room = Cache::get("room_" . strtoupper($roomId));

        if (!$room) {
            throw new \Exception("Room not found after playWord");
        }

        broadcast(new WordPlayed($roomId, $room));

        return response()->json($result);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function startVoting(string $roomId): JsonResponse
    {
        try {
            $result = $this->gameService->startVoting($roomId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
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

    public function results(string $roomId)
    {
        try {
            return response()->json($this->gameService->getResults($roomId));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
