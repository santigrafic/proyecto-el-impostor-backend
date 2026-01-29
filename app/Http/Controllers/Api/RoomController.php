<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RoomService;
use Illuminate\Http\Request;

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
            'nickname' => 'nullable|string'
        ]);

        try {
            // Forzar mayúsculas en roomId
            $roomId = strtoupper($roomId);

            $result = $this->roomService->joinRoom(
                $roomId,
                $data['playerId'],
                $data['nickname'] ?? null
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404); // devuelve 404 en lugar de 500
        }
    }

    public function start(string $roomId, Request $request)
    {
        $data = $request->validate([
            'hostId' => 'required|string'
        ]);

        return response()->json(
            $this->roomService->startGame($roomId, $data['hostId'])
        );
    }

    public function me(string $roomId, Request $request)
    {
        $data = $request->validate([
            'playerId' => 'required|string'
        ]);

        return response()->json(
            $this->roomService->getPlayerInfo($roomId, $data['playerId'])
        );
    }

    public function state(string $roomId)
    {
        return response()->json(
            $this->roomService->getRoomState($roomId)
        );
    }
}
