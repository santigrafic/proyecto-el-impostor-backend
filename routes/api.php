<?php

use App\Http\Controllers\Api\RoomController;
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

