<?php

use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Unirse a una room
Route::post('/rooms/{roomId}/join', [RoomController::class, 'join']);

// Ver estado de la room
Route::get('/rooms/{roomId}', [RoomController::class, 'show']);

// Crear room
Route::post('/rooms', [RoomController::class, 'create']);

// Iniciar partida

// Obtener info de cada jugador (/rooms/{roomId}/me?playerId=xxx)
Route::get('/rooms/{roomId}/me', [RoomController::class, 'me']);

// Devuelve es estado de la partida a todos los jugadores
Route::get('/rooms/{roomId}/state', [RoomController::class, 'state']);

// Un jugador sale de la room
Route::post('rooms/{roomId}/exit', [RoomController::class, 'exitRoom']);

Route::prefix('games')->group(function () {
    Route::get('{roomId}/state', [GameController::class, 'state']);
    Route::post('{roomId}/me', [GameController::class, 'me']);
    Route::post('{roomId}/word', [GameController::class, 'playWord']);
    Route::post('{roomId}/exit', [GameController::class, 'exitGame']);
});

Route::post('/games/{roomId}/start-voting', [GameController::class, 'startVoting']);
Route::post('/games/{roomId}/vote', [GameController::class, 'vote']);

Route::get('/games/{roomId}/results', [GameController::class, 'results']);

// Rutas CRUD Games
// Route::get('/games', [GameController::class, 'index']);
// Route::get('/games/{id}', [GameController::class, 'show']);
Route::middleware('game.token')->group(function () {
    //Route::post('/games', [GameController::class, 'store']);
    Route::post('/rooms/{roomId}/start', [RoomController::class, 'start']);
    Route::post('/games/{roomId}/finish', [GameController::class, 'finish']);
});

// Rutas Usuarios
Route::apiResource('users', UserController::class);
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
Route::get('ranking', [UserController::class, 'ranking']);

// Login
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Register
Route::post('/register', [AuthController::class, 'register']);

// Ruta par aahcer ping en render
Route::get('/api/ping', function () {
    return response()->json(['ok' => true]);
});

// Ruta para probar la BBDD
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return ['status' => 'OK'];
    } catch (\Throwable $e) {
        return [
            'status' => 'ERROR',
            'message' => $e->getMessage(),
        ];
    }
});

// Ruta para probar .env
Route::get('/debug-env-db', function () {
    return [
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'DB_HOST' => env('DB_HOST'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
        'config_default' => config('database.default'),
        'token' => env('GAME_API_TOKEN'),
    ];
});