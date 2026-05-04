<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameFinished implements ShouldBroadcast
{
    public string $roomId;
    public array $room;

    public function __construct(string $roomId, array $room)
    {
        $this->roomId = $roomId;
        $this->room = $room;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'game.finished';
    }

    public function broadcastWith()
    {
        return [
            'gameState' => [
            ...$this->room,
            'players' => array_values($this->room['players']),
        ]
        ];
    }
}