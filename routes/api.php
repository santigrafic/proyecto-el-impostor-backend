<?php

use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\GameController;
use Illuminate\Support\Facades\Route;

// Unirse a una room
Route::post('/rooms/{roomId}/join', [RoomController::class, 'join']);

// Ver estado de la room
Route::get('/rooms/{roomId}', [RoomController::class, 'show']);

// Crear room
Route::post('/rooms', [RoomController::class, 'create']);

// Iniciar partida
Route::post('/rooms/{roomId}/start', [RoomController::class, 'start']);

// Obtener info de cada jugador (/rooms/{roomId}/me?playerId=xxx)
Route::get('/rooms/{roomId}/me', [RoomController::class, 'me']);

// Devuelve es estado de la aprtida a todos los jugadores
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
