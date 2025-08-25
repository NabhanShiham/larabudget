<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile() {
        return $this->hasOne(Profile::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
    public function purchases(){
        return $this->hasMany(Purchase::class);
    }
    public function friends(){
        return $this->belongsToMany(User::class, 'user_friends', 'user_id', 'friend_id');
    }
    public function friendOf(){
        return $this->belongsToMany(User::class, 'user_friends', 'user_id', 'friend_id');
    }
    public function sentFriendRequests(){
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }
    public function receivedFriendRequests(){
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }
    public function ownedCollaborations(){
        return $this->hasMany(Collaborate::class, 'owner_id');
    }
    public function collaborations(){
        return $this->belongsToMany(Collaborate::class, 'collaborate_user', 'user_id', 'collaborate_id')->withTimestamps();
    }
    public function contributions(){
        return $this->hasMany(Contribution::class);
    }
}
