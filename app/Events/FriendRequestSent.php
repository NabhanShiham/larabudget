<?php

namespace App\Events;

use App\Models\FriendRequest; // Add this import
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $friendRequest;

    public function __construct(FriendRequest $friendRequest)
    {
        $this->friendRequest = $friendRequest;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->friendRequest->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'friendRequest' => [
                'id' => $this->friendRequest->id,
                'sender' => [
                    'id' => $this->friendRequest->sender->id,
                    'name' => $this->friendRequest->sender->name,
                    'email' => $this->friendRequest->sender->email,
                ],
                'receiver_id' => $this->friendRequest->receiver_id,
                'status' => $this->friendRequest->status,
                'created_at' => $this->friendRequest->created_at,
            ]
        ];
    }
}