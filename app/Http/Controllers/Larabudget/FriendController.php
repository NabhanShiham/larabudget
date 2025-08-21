<?php

namespace App\Http\Controllers\Larabudget;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\FriendRequest;
use App\Events\FriendRequestSent;


class FriendController extends Controller
{
    public function search(Request $request){
        $query = $request->input('query');
       $users = User::where('name', 'LIKE', '%' . $query . '%')->get();
        return response()->json([
            'users' => $users
        ], 200);
    }

    public function getCurrentUser(){
    $currentUser = Auth::user();
    return response()->json([
    'user' => $currentUser
    ], 200);
    }


public function sendFriendRequest(Request $request)
{
    try {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id'
        ]);

        $existingRequest = FriendRequest::where('sender_id', auth()->id())
            ->where('receiver_id', $validated['receiver_id'])
            ->first();

        if ($existingRequest) {
            return response()->json([
                'message' => 'Friend request already sent'
            ], 422);
        }

        $friendRequest = FriendRequest::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'status' => 'pending'
        ]);

        $friendRequest->load('sender', 'receiver');

        NotificationService::sendFriendRequestNotification($friendRequest, $sender, $recipient);

        return response()->json([
            'message' => 'Friend request sent successfully',
            'friendRequest' => $friendRequest
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Friend request error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error sending friend request',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error'
        ], 500);
    }
}


    public function respondToFriendRequest(Request $request){
        $user = $request->user();
        $validated = $request->validate([
            'request_id' => 'required|exists:friend_requests,id',
            'action' => 'required|in:accept,reject',
        ]);
        $friendRequest = FriendRequest::where('id', $validated['request_id'])
            ->where('receiver_id', $user->id)
            ->firstOrFail();
        
        $friendRequest->status = $validated['action'] == 'accept' ? 'accepted' : 'rejected';
        $friendRequest->save();
        if($validated['action'] === 'accept'){
            $user->friends()->syncWithoutDetaching([$friendRequest->sender_id]);
        }
        return response()->json(['message' => 'Friend Request '. $validated['action'] . 'ed.']);
    }
public function listFriends(Request $request)
{
    $user = $request->user();

    $friends = $user->friends()
        ->select('users.id', 'users.name', 'users.email') 
        ->get();

    return response()->json([
        'friends' => $friends
    ]);
}

    public function pendingRequests(Request $request)
{
    $user = $request->user();

    $requests = FriendRequest::where('receiver_id', $user->id)
        ->where('status', 'pending')
        ->with('sender:id,name,email')
        ->get();

    return response()->json(['requests' => $requests]);
}

}
