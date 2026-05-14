<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Events\VoteRegistered;
use App\Events\GameFinished;

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

        // if ($room['status'] !== 'playing') {
        //    throw new \Exception('Game not started');
        //}

        $impostorNickname = $room['players'][$room['impostorId']]['nickname'] ?? null;

        //$playedWordsCount = 0;

        $wordsByPlayer = [];

        foreach ($room['players'] as $playerId => $player) {
            $wordsByPlayer[] = [
                'nickname' => $player['nickname'],
                'words' => $room['playedWords'][$playerId] ?? [],
            ];
        }

        return [
            'roomId' => $roomId,
            'status' => $room['status'],
            'game_id' => $room['game_id'] ?? null,
            //'players' => array_values(array_map(fn($p) => ['id' => $p['id'], 'nickname' => $p['nickname']], $room['players'])),
            'players' => array_values($room['players']),
            'playedWordsCount' => array_sum(array_map('count', $room['playedWords'] ?? [])),
            'totalPlayers' => count($room['players']),
            'wordsByPlayer' => $wordsByPlayer,
            'votesCount' => count($room['votes'] ?? []),
            'votes' => $room['votes'] ?? [],
            'winner' => $room['winner'] ?? null,
            'impostorNickname' => $impostorNickname ?? null,
        ];
    }

    /**
     * Información privada del jugador (rol y palabra)
     */
    public function getPlayerGameInfo(string $roomId, string $playerId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");
        $playerId = (string) $playerId;

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if (!isset($room['players'][$playerId])) {
            throw new \Exception('Player not in room');
        }

        if (!in_array($room['status'], ['playing', 'voting', 'finished'])) {
            throw new \Exception('Invalid game state');
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
            'isMyTurn' => (string) $room['currentTurn'] === (string) $playerId,
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
        $playerId = (string) $playerId;

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if ($room['status'] !== 'playing') {
            throw new \Exception('Game not started');
        }

        if (!isset($room['players'][$playerId])) {
            throw new \Exception('Player not in room');
        }

        //if ($room['currentTurn'] === null) {
        //    throw new \Exception('No active turn');
        //}

        // Inicializar si no existe
        if (!isset($room['playedWords'][$playerId])) {
            $room['playedWords'][$playerId] = [];
        }

        // Comprobar si ya ha usado todas sus palabras
        if (count($room['playedWords'][$playerId] ?? []) >= $room['wordsPerPlayer']) {
            throw new \Exception('Player already played all words');
        }

        // Comprobar turno
        logger()->info('PLAY WORD DEBUG', [
            'playerId' => $playerId,
            'currentTurn' => $room['currentTurn'],
            'playedWordsKeys' => array_keys($room['playedWords']),
            'playersKeys' => array_keys($room['players']),
        ]);
        
        if ($playerId !== (string)$room['currentTurn']) {
            throw new \Exception('No es tu turno');
        }

        // FORMATEAMOS LA PALABRA
        // Limpiar espacios
        $word = trim($word);
        // Comprobar palabra vacía
        if ($word === '') {
            throw new \Exception('La palabra no puede estar vacía');
        }
        // Pasar a mayúsculas
        $word = mb_strtoupper($word, 'UTF-8');

        // Añadir palabra
        $room['playedWords'][$playerId][] = $word;

        // ¿Todos los jugadores han usado todas sus palabras?
        $allPlayersFinished = true;

        foreach ($room['players'] as $id => $_) {
            if (count($room['playedWords'][$id] ?? []) < $room['wordsPerPlayer']) {
                $allPlayersFinished = false;
                break;
            }
        }

        if ($allPlayersFinished) {
            $room['currentTurn'] = null;
        } else {
            // Elegir siguiente jugador con turnos restantes
            $playerIds = array_keys($room['players']);
            $currentIndex = array_search($playerId, $playerIds);

            for ($i = 1; $i <= count($playerIds); $i++) {
                $nextIndex = ($currentIndex + $i) % count($playerIds);
                $nextId = $playerIds[$nextIndex];

                if (count($room['playedWords'][$nextId] ?? []) < $room['wordsPerPlayer']) {
                    $room['currentTurn'] = $nextId;
                    break;
                }
            }
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

    public function startVoting(string $roomId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        logger()->info('START VOTING', [
            'roomId' => $roomId,
            'status' => $room['status'] ?? null,
        ]);


        if (!$room) {
            throw new \Exception('Room not found');
        }

        //if ($room['status'] !== 'playing') {
        //    throw new \Exception('Game is not in playing state');
        //}

        $room['status'] = 'voting';
        $room['votes'] = [];

        Cache::put("room_$roomId", $room, 3600);

        return ['message' => 'Voting started'];
    }

    public function vote(string $roomId, string $playerId, string $votedPlayerId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");
        $playerId = (string) $playerId;

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

        if ($playerId === $votedPlayerId) {
            throw new \Exception('You cannot vote yourself');
        }

        if (isset($room['votes'][$playerId])) {
            throw new \Exception('Player already voted');
        }

        $room['votes'][$playerId] = $votedPlayerId;

        Cache::put("room_$roomId", $room, 3600);

        broadcast(new VoteRegistered($roomId, $room));

        // ¿Han votado todos?
        if (count($room['votes']) === count($room['players'])) {
            $room['status'] = 'finished';

            Cache::put("room_$roomId", $room, 3600);

            broadcast(new GameFinished($roomId, $room));
        }

        return ['message' => 'Vote registered'];
    }

    public function getResults(string $roomId): array
    {
        $roomId = strtoupper($roomId);
        $room = Cache::get("room_$roomId");

        if (!$room) {
            throw new \Exception('Room not found');
        }

        if ($room['status'] !== 'finished') {
            throw new \Exception('Game not finished yet');
        }

        $impostorId = $room['impostorId'] ?? null;
        $voteCounts = array_count_values($room['votes'] ?? []);
        $impostorNickname = $room['players'][$room['impostorId']]['nickname'] ?? null;

        $maxVotes = max(array_values($voteCounts) ?: [0]);
        $ties = array_filter($voteCounts, fn($v) => $v === $maxVotes);

        // Si hay empate o el impostor tiene menos votos que el máximo, gana el impostor
        $winner = (count($ties) > 1 || ($impostorId && ($voteCounts[$impostorId] ?? 0) < $maxVotes))
            ? 'impostor'
            : 'players';

        return [
            'winner' => $winner,
            'votes' => $voteCounts,
            'impostorNickname' => $impostorNickname,
        ];
    }

    public function store(array $data): Game
    {
        return DB::transaction(function () use ($data) {

            $game = Game::create([
                'theme' => $data['theme'],
                'word' => $data['word'],
                'winner' => $data['winner'],
                'started_at' => $data['started_at'],
                'finished_at' => $data['finished_at'],
            ]);

            foreach ($data['players'] as $player) {

                $user = User::find($player['id']);
                if (!$user) continue;

                $game->users()->attach($user->id, [
                    'role' => $player['role'],
                ]);

                // stats
                $user->games_played++;

                if ($player['role'] === 'impostor') {
                    $user->times_impostor++;
                }

                $isWinner =
                    ($data['winner'] === 'impostor' && $player['role'] === 'impostor')
                    || ($data['winner'] === 'players' && $player['role'] !== 'impostor');

                if ($isWinner) {
                    $user->games_won++;
                }

                $user->save();
            }

            return $game;
        });
    }

    public function finish(Game $game, array $data): Game
    {
        DB::transaction(function () use ($game, $data) {

            // 1. Actualizar game
            $game->update([
                'winner' => $data['winner'],
                'finished_at' => now(),
            ]);

            // 2. Recorrer jugadores
            foreach ($data['players'] as $player) {

                // Ignorar guests
                if ($player['isGuest']) {
                    continue;
                }

                $user = User::find($player['id']);

                if (!$user) {
                    continue;
                }

                // 3. Pivot game_user
                $game->users()->syncWithoutDetaching([
                    $user->id => [
                        'role' => $player['role']
                    ]
                ]);

                // 4. Stats
                $user->increment('games_played');

                // Veces impostor
                if ($player['role'] === 'impostor') {
                    $user->increment('times_impostor');
                }

                // Victoria
                $hasWon =
                    ($data['winner'] === 'impostor' && $player['role'] === 'impostor')
                    ||
                    ($data['winner'] === 'players' && $player['role'] === 'player');

                if ($hasWon) {
                    $user->increment('games_won');
                }
            }
        });

        return $game->fresh();
    }
}

