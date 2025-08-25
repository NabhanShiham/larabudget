<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_id',
        'user_id',
        'title',
        'message',
        'type', 
        'read'
    ];
    
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
