<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RoomService
{
    // Array origianl de palabras por si fallan las api
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
            'theme' => 'default',
            //'word' => $this->apiRandomWord(),
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
        // NICKNAME SEGUN COUNT
        // $num = count($room['players']) + 1;
        // return 'Player_' . str_pad($num, 2, '0', STR_PAD_LEFT);

        // Sacamos todos los nicknames actuales
        $used = array_map(
            fn($p) => $p['nickname'],
            $room['players']
        );

        // Empezamos desde 1
        $i = 1;

        while (true) {
            $nickname = 'Player_' . str_pad($i, 2, '0', STR_PAD_LEFT);

            if (!in_array($nickname, $used)) {
                return $nickname;
            }

            $i++;
        }
    }

    public function startGame(string $roomId, string $hostId, string $theme, $wordsPerPlayer = 3): array
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

        if (count($room['players']) < 3) {
            throw new \Exception('Not enough players');
        }

        // Palabras por jugador
        $room['wordsPerPlayer'] = $wordsPerPlayer; // por defecto 3
        foreach ($room['players'] as $id => $_) {
            $room['playedWords'][$id] = []; // inicializar array vacío por jugador
        }

        // Turnos aleatorios
        $playerIds = array_keys($room['players']);
        $room['currentTurn'] = $playerIds[array_rand($playerIds)];

        // Asignar impostor
        $playerIds = array_keys($room['players']);
        $impostorId = $playerIds[array_rand($playerIds)];

        foreach ($room['players'] as $id => &$player) {
            $player['role'] = ($id === $impostorId) ? 'impostor' : 'player';
        }

        $room['impostorId'] = $impostorId;

        //Generar palabra
        $theme = $theme ?? 'default';
        $room['word'] = $this->randomWord($theme);
        //$room['word'] = $this->randomWordArray();

        $room['status'] = 'playing';

        Cache::put("room_$roomId", $room, 3600);

        return [
            'status' => 'started'
        ];
    }

    //Generar palabra desde array
    private function randomWordArray(): string
    {
        return $this->words[array_rand($this->words)];
    }

    // Generar palabra random desde una api externa
    private function apiRandomWord(string $theme): string
    {
        if ($theme == 'default') {
        try {
            $response = Http::get('https://random-word-api.herokuapp.com/word?diff=1&lang=es');

            if ($response->status() == 200) {

                $body = $response->body(); // obtenemos el texto crudo
                $data = json_decode($body, true); // lo convertimos a array

                if (is_array($data) && isset($data[0])) {
                    return ucfirst($data[0]); // devolvemos la palabra con mayúscula inicial
                }
            }
        } catch (\Exception $e) {
            Log::error('Error obteniendo palabra: ' . $e->getMessage());
        }
        // Fallback por si la API falla
        return $this->randomWord($theme);
        } else {
            return $this->randomWord($theme);
        }
    }

    // Generar palabra random por temática elegida desde json
    private function randomWord(string $theme): string
    {
        $path = storage_path("app/words/{$theme}.json");

        if (!file_exists($path)) {
            throw new \Exception("Theme not found");
        }

        $json = file_get_contents($path);

        $data = json_decode($json, true);

        if (!isset($data['words']) || empty($data['words'])) {
            throw new \Exception("No words available for this theme");
        }

        return mb_strtoupper($data['words'][array_rand($data['words'])], 'UTF-8');
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
            'word' => $player['role'] === 'player' ? $room['word'] : null,
            'currentTurn' => $room['currentTurn'],
            'wordsPerPlayer' => $room['wordsPerPlayer'], // para frontend
            'words' => $room['playedWords'][$playerId] ?? []
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

    public function removePlayer(string $roomId, string $playerId)
    {
        $roomId = strtoupper($roomId);

        $room = Cache::get("room_$roomId");

        if (!$room) {
            return null;
        }

        unset($room['players'][$playerId]);

        /*$room['players'] = array_values(
            array_filter($room['players'], function ($p) use ($playerId) {
                return $p['id'] !== $playerId;
            })
        );*/

        // si el host se fue, reasignar host
        /*if ($room['hostId'] === $playerId) {
            $room['hostId'] = $room['players'][0]['id'] ?? null;
        }*/

        Cache::put("room_$roomId", $room, 3600);

        return $room;
    }
}
