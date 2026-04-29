<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RoomService;
use Illuminate\Http\Request;
use App\Events\PlayerJoined;

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

        /*
         * Emitimos el evento websocket para notificar
         * a todos los jugadores de la sala.
         */
        $room = \App\Models\Room::with('players')
            ->where('id', $roomId)
            ->firstOrFail();

        broadcast(new PlayerJoined($room))->toOthers();

        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 404);
    }
}

    public function start(string $roomId, Request $request)
{
    $data = $request->validate([
        'hostId' => 'required|string'
    ]);

    try {
        // Iniciar la partida
        $result = $this->roomService->startGame(
            strtoupper($roomId),
            $data['hostId']
        );

        // Recuperar la sala actualizada con sus jugadores
        $room = \App\Models\Room::with('players')
            ->where('id', strtoupper($roomId))
            ->firstOrFail();

        // Notificar a todos los clientes conectados
        broadcast(new GameStarted($room))->toOthers();

        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 400);
    }
}

    public function state(string $roomId)
    {
        return response()->json(
            $this->roomService->getRoomState($roomId)
        );
    }
}
