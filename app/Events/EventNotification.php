<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $userId;
    public $uniqueId;

    public function __construct($data, $userId, $uniqueId)
    {
        $this->data = $data;
        $this->userId = $userId;
        $this->uniqueId = $uniqueId;
    }

    public function broadcastOn()
    {
        return new Channel('notifications.' . $this->userId.'-' . $this->uniqueId);
    }

    public function broadcastAs()
    {
        return 'notifications';
    }
}
