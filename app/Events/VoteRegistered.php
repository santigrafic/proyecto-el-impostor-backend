<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Services\GameService;

class VoteRegistered implements ShouldBroadcastNow
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
        return 'vote.registered';
    }

    public function broadcastWith()
{
    $service = new GameService();

    return [
        'gameState' => $service->getGameState($this->roomId)
    ];
}
}