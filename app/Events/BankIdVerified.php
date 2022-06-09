<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BankIdVerified implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $userId;
    public $uniqueId;

    public function __construct($data, $user, $uniqueId)
    {
        $this->data = $data;
        $this->userId = $user->id;
        $this->uniqueId = $user->unique_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('bank-id-verified.' . $this->userId.'-' . $this->uniqueId);
    }

    public function broadcastAs()
    {
        return 'bankIdVerified.event';
    }
}
