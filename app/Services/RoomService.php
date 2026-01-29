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
            'hostId' => null,
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
    public function joinRoom(string $roomId, string $playerId, ?string $nickname): array
    {
        $roomId = strtoupper($roomId);

        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if ($room['status'] !== 'waiting') {
            throw new \Exception('Room is already started');
        }

        if (isset($room['players'][$playerId])) {
            throw new \Exception('Player already in room');
        }

        if (!$nickname) {
            $nickname = $this->generateGuestNickname($room);
        }

        $room['players'][$playerId] = [
            'id' => $playerId,
            'nickname' => $nickname,
            'role' => $room['players'][$playerId]['role'] ?? 'player'
        ];

        // Primer jugador = host
        if ($room['hostId'] === null) {
            $room['hostId'] = $playerId;
        }

        Cache::put("room_$roomId", $room, 3600);

        return [
            'id' => $playerId,
            'nickname' => $nickname,
            'role' => 'player'
        ];
    }


    public function getPublicRoom(string $roomId): array
    {
        $roomId = strtoupper($roomId);

        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        // Devolver nickname
        $players = array_map(function ($p) use ($room) {
            $role = ($p['id'] === $room['impostorId']) ? 'impostor' : 'player';
            return [
                'nickname' => $p['nickname'],
                'role' => $role
            ];
        }, $room['players']);

        return [
            'hostId' => $room['hostId'],
            'players' => array_values(
                array_map(fn($p) => [
                    'id' => $p['id'],
                    'nickname' => $p['nickname'],
                ], $room['players'])
            ),
            'status' => $room['status']
        ];
    }

    //Generar un nickname para los invitados
    private function generateGuestNickname(array $room): string
    {
        $num = count($room['players']) + 1;
        return 'Invitado' . str_pad($num, 2, '0', STR_PAD_LEFT);
    }

    //Generar palabra
    private function randomWord(): string
    {
        return $this->words[array_rand($this->words)];
    }

    public function startGame(string $roomId, string $hostId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if ($room['status'] !== 'waiting') {
            throw new \Exception('Game already started');
        }

        if ($room['hostId'] !== $hostId) {
            throw new \Exception('Only host can start the game');
        }

        if (count($room['players']) < 2) {
            throw new \Exception('Not enough players');
        }

        // Asignar impostor
        $playerIds = array_keys($room['players']);
        $impostorId = $playerIds[array_rand($playerIds)];

        foreach ($room['players'] as $id => &$player) {
            $player['role'] = ($id === $impostorId) ? 'impostor' : 'player';
        }

        $room['impostorId'] = $impostorId;
        $room['status'] = 'playing';

        Cache::put("room_$roomId", $room, 3600);

        return [
            'status' => 'started'
        ];
    }

    public function getPlayerInfo(string $roomId, string $playerId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if (!isset($room['players'][$playerId])) {
            throw new \Exception('Player not in room');
        }

        $player = $room['players'][$playerId];

        return [
            'nickname' => $player['nickname'],
            'role' => $player['role'],
            'word' => $player['role'] === 'player' ? $room['word'] : null
        ];
    }

    public function getRoomState(string $roomId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        return [
            'status' => $room['status'],
            'hostId' => $room['hostId'],
            'players' => array_values(
                array_map(fn($p) => [
                    'id' => $p['id'],
                    'nickname' => $p['nickname'],
                ], $room['players'])
            )
        ];
    }
}
