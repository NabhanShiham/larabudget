<?php

namespace App\Events;

use App\Models\FriendRequest; 
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
        $this->sender = $sender;
        $this->receiver = $receiver;

        $this->hideSensitiveData();
    }

    public function broadcastOn()
    {
        return new Channel('notifications.' . $this->receiver->id);
    }

    public function broadcastAs()
    {
        return 'friend-request-sent';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->friendRequest->id,
            'sender_name' => $this->sender->name,
            'sender_id' => $this->sender->id,
            'message' => "You have a new friend request from {$this->sender->name}",
            'type' => 'friend_request',
            'created_at' => now()->toDateTimeString(),
            'read' => false
        ];
    }
    protected function hideSensitiveData()
    {
        unset($this->receiver->email, $this->receiver->password);
        unset($this->sender->email, $this->sender->password);
    }
}