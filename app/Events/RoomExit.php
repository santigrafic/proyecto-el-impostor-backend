<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomExit implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
        return 'room.exit';
    }

    public function broadcastWith()
    {
        return [
            'room' => $this->room
        ];
    }
}