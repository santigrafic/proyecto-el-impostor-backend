<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class GameService
{
    /**
     * Obtener estado global de la partida
     */
    public function getGameState(string $roomId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if ($room['status'] !== 'playing') {
            throw new \Exception('Game not started');
        }

        //$impostorNickname = $room['players'][$room['impostorId']]['nickname'] ?? null;
        //$winnerNickname = $room['winnerId'] ? $room['players'][$room['winnerId']]['nickname'] : null;

        return [
            'status' => $room['status'],
            'players' => array_values(
                array_map(fn($p) => [
                    'id' => $p['id'],
                    'nickname' => $p['nickname'],
                ], $room['players'])
            ),
            'playedWordsCount' => count($room['playedWords'] ?? []),
            'totalPlayers' => count($room['players']),
            'words' => array_values(
                array_map(fn($w) => [
                    'word' => $w
                ], $room['playedWords'] ?? [])
            ),
            'votesCount' => count($room['votes'] ?? []),
            //'impostorNickname' => $impostorNickname,
            //'winnerNickname' => $winnerNickname,
        ];
    }

    /**
     * Información privada del jugador (rol y palabra)
     */
    public function getPlayerGameInfo(string $roomId, string $playerId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if (!isset($room['players'][$playerId])) {
            throw new \Exception('Player not in room');
        }

        if ($room['status'] !== 'playing') {
            throw new \Exception('Game not started');
        }

        $player = $room['players'][$playerId];

        return [
            'playerId' => $playerId,
            'nickname' => $player['nickname'],
            'role' => $player['role'],
            'word' => $player['role'] === 'player' ? $room['word'] : null,
            'words' => $room['playedWords'][$playerId] ?? [],
            'wordsPerPlayer' => $room['wordsPerPlayer'],
            'hasPlayed' => count($room['playedWords'][$playerId] ?? []) >= $room['wordsPerPlayer'],
            'isMyTurn' => $room['currentTurn'] === $playerId,
            'hasVoted' => isset($room['votes'][$playerId]) || false,
        ];
    }

    /**
     * El jugador envía su palabra
     */
    public function playWord(string $roomId, string $playerId, string $word): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if ($room['status'] !== 'playing') {
            throw new \Exception('Game not started');
        }

        if (!isset($room['players'][$playerId])) {
            throw new \Exception('Player not in room');
        }

        // Inicializar si no existe
        if (!isset($room['playedWords'][$playerId])) {
            $room['playedWords'][$playerId] = [];
        }

        // Comprobar si ya ha usado todas sus palabras
        if (count($room['playedWords'][$playerId]) >= $room['wordsPerPlayer']) {
            throw new \Exception('Player already played all words');
        }

        // Comprobar turno
        if ($playerId !== $room['currentTurn']) {
            throw new \Exception('No es tu turno');
        }

        // Añadir palabra
        $room['playedWords'][$playerId][] = $word;

        // Elegir siguiente jugador con turnos restantes
        $playersWithTurnsLeft = [];

        foreach ($room['players'] as $id => $_) {
            if (count($room['playedWords'][$id]) < $room['wordsPerPlayer']) {
                $playersWithTurnsLeft[] = $id;
            }
        }

        $room['currentTurn'] = !empty($playersWithTurnsLeft)
            ? $playersWithTurnsLeft[array_rand($playersWithTurnsLeft)]
            : null;

        // ¿Todos los jugadores han usado todas sus palabras?
        $allPlayersFinished = true;

        foreach ($room['players'] as $id => $_) {
            if (count($room['playedWords'][$id]) < $room['wordsPerPlayer']) {
                $allPlayersFinished = false;
                break;
            }
        }

        if ($allPlayersFinished) {
            $room['status'] = 'voting';
            $room['currentTurn'] = null;
        }

        // Guardar estado
        Cache::put("room_$roomId", $room, 3600);

        return [
            'message' => 'Word played successfully',
            'playedWords' => count($room['playedWords']),
            'totalPlayers' => count($room['players']),
            'status' => $room['status']
        ];
    }

    public function vote(string $roomId, string $playerId, string $votedPlayerId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if ($room['status'] !== 'voting') {
            throw new \Exception('Voting phase not active');
        }

        if (!isset($room['players'][$playerId])) {
            throw new \Exception('Player not in room');
        }

        if (!isset($room['players'][$votedPlayerId])) {
            throw new \Exception('Voted player not in room');
        }

        // No puede votarse a sí mismo
        if ($playerId === $votedPlayerId) {
            throw new \Exception('You cannot vote yourself');
        }

        // Evitar doble voto
        if (isset($room['votes'][$playerId])) {
            throw new \Exception('Player already voted');
        }

        $room['votes'][$playerId] = $votedPlayerId;

        // ¿Han votado todos?
        if (count($room['votes']) === count($room['players'])) {
            $room['status'] = 'finished';
        }

        Cache::put("room_$roomId", $room, 3600);

        return [
            'message' => 'Vote registered'
        ];
    }
}
