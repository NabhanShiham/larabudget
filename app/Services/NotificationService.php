<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NotificationEvent;
use App\Events\FriendRequestSent;

class NotificationService
{
    public static function send($userId, $title, $message, $type = 'info')
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type
        ]);

        event(new NotificationEvent([
            'id' => $notification->id,
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'read' => false,
            'created_at' => $notification->created_at
        ]));

        return $notification;
    }

    public static function sendFriendRequestNotification($friendRequest, $sender, $receiver)
{
    $notification = Notification::create([
        'user_id' => $receiver->id,
        'title' => 'New Friend Request',
        'message' => "You have a new friend request from: {$sender->name}",
        'type' => 'friend_request',
    ]);
    
    return $notification;
}
}