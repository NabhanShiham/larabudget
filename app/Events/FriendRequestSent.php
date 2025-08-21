<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $sender;
    public $receiver;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        $this->sender = $sender; 
        $this->receiver = $receiver;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return new PrivateChannel('user' . $this->receiver->id);
    }

    public function broadcastWith(){
        return [
            'message' => "{$this->sender->name} sent you a friend request.",
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
        ];
    }
}
