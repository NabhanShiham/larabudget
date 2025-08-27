<?php

namespace App\Http\Controllers\Larabudget;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $senderId = $request->input('sender_id');
        $recipientId = $request->input('recipient_id');

        $messages = Message::where(function ($query) use ($senderId, $recipientId) {
            $query->where('sender_id', $senderId)
                  ->where('recipient_id', $recipientId);
        })->orWhere(function ($query) use ($senderId, $recipientId) {
            $query->where('sender_id', $recipientId)
                  ->where('recipient_id', $senderId);
        })->orderBy('created_at')->get();

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'recipient_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create($validated);

        return response()->json(['message' => $message], 201);
    }

}