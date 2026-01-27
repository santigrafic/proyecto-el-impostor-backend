<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RoomService
{
    private array $words = [
        'Pizza',
        'Avión',
        'Playa',
        'Hospital',
        'Cine'
    ];

    // Crear room
    public function createRoom(): string
    {
        $roomId = strtoupper(substr(uniqid(), -6));

        $room = [
            'players' => [],
            'word' => $this->randomWord(),
            'impostorId' => null,
            'status' => 'waiting'
        ];

        // Guardar en cache 1 hora
        Cache::put("room_$roomId", $room, 3600);

        return $roomId;
    }

    // Unirse a una room
    public function joinRoom(string $roomId, string $playerId, string $nickname): array
    {
        $roomId = strtoupper($roomId);

        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        $room['players'][$playerId] = [
            'id' => $playerId,
            'nickname' => $nickname
        ];

        // Si todavía no hay impostor, lo asignamos al azar
        if ($room['impostorId'] === null && count($room['players']) >= 2) {
            $playerIds = array_keys($room['players']);
            $room['impostorId'] = $playerIds[array_rand($playerIds)];
            $room['status'] = 'playing';
        }

        // Guardar cambios
        Cache::put("room_$roomId", $room, 3600);

        // Devolver info al jugador
        if ($playerId === $room['impostorId']) {
            return [
                'role' => 'impostor',
                'word' => null
            ];
        }

        return [
            'role' => 'player',
            'word' => $room['word']
        ];
    }

    public function getPublicRoom(string $roomId): array
    {
        $roomId = strtoupper($roomId);

        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        // Devolver nickname + role
        $players = array_map(function ($p) use ($room) {
            $role = ($p['id'] === $room['impostorId']) ? 'impostor' : 'player';
            return [
                'nickname' => $p['nickname'],
                'role' => $role
            ];
        }, $room['players']);

        return [
            'players' => array_values($players),
            'status' => $room['status']
        ];
    }

    private function randomWord(): string
    {
        return $this->words[array_rand($this->words)];
    }
}
