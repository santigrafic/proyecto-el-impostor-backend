<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RoomService;
use Illuminate\Http\Request;
use App\Events\PlayerJoined;
use App\Events\GameStarted;
use App\Events\RoomExit;
use App\Events\RoomClosed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    protected $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $roomId)
    {
        try {
            return response()->json(
                $this->roomService->getPublicRoom($roomId)
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function create()
    {
        $roomId = $this->roomService->createRoom();

        return response()->json([
            'roomId' => $roomId
        ]);
    }

    public function join(string $roomId, Request $request)    
    {
    $data = $request->validate([
        'playerId' => 'required|string',
        'nickname' => 'nullable|string',
    ]);

    try {
        // Forzar mayúsculas en roomId
        $roomId = strtoupper($roomId);

        $result = $this->roomService->joinRoom(
            $roomId,
            $data['playerId'],
            $data['nickname'] ?? null
        );

        // Emitimos el evento websocket para notificar a todos los jugadores de la sala.
        broadcast(new PlayerJoined($roomId, $this->roomService->getRoomState($roomId)));

        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function start(string $roomId, Request $request)
{
    $data = $request->validate([
        'hostId' => 'required|string'
    ]);

    try {
        $roomId = strtoupper($roomId);

        $result = $this->roomService->startGame(
            $roomId,
            $data['hostId']
        );

        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception("Room not found in cache after start");
        }

        broadcast(new GameStarted($roomId, $room));

        return response()->json($result);

    } catch (\Exception $e) {

        switch ($e->getMessage()) {

        case 'Not enough players':
            return response()->json([
                'error' => 'Se necesitan al menos 3 jugadores'
            ], 400);

        case 'Room not found':
            return response()->json([
                'error' => 'La sala no existe'
            ], 404);

        default:
            return response()->json([
                'error' => 'Error interno'
            ], 500);
    }
    }
}

    public function state(string $roomId)
    {
        return response()->json(
            $this->roomService->getRoomState($roomId)
        );
    }

    public function exitRoom(Request $request, string $roomId)
    {
        $playerId = $request->input('playerId');

        $room = Cache::get("room_$roomId");

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        // CASO 1: jugador host
        if ($room['hostId'] === $playerId) {

            Cache::forget("room_$roomId");

            broadcast(new RoomClosed($roomId))->toOthers();

            return response()->json([
                'ok' => true,
                'reason' => 'host_left'
            ]);
        }
    
        // CASO 2: jugador normal
        $room = $this->roomService->removePlayer($roomId, $playerId);

        Cache::put("room_$roomId", $room, 3600);

        broadcast(new RoomExit($roomId, $room))->toOthers();

        return response()->json(['ok' => true]);
    }
}
