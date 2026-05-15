<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomClosed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $roomId;

    public function __construct(string $roomId)
    {
        $this->roomId = $roomId;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'room.closed';
    }

    public function broadcastWith()
    {
        return [
            'roomId' => $this->roomId,
            'message' => 'Host left. Room closed.'
        ];
    }
}