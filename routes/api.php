<?php

use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

// Unirse a una room
Route::post('/rooms/{roomId}/join', [RoomController::class, 'join']);

// Ver estado de la room
Route::get('/rooms/{roomId}', [RoomController::class, 'show']);

// Crear room
Route::post('/rooms', [RoomController::class, 'create']);