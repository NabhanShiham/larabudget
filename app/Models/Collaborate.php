<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collaborate extends Model
{
    protected $fillable = ['ownerId', 'goal', 'currentProgress', 'status'];
    public function owner(){
        return $this->belongsTo(User::class, 'ownerId');
    }
    public function members(){
        return $this->belongsToMany(User::class, 'collaborate_user', 'collaborate_id', 'user_id')->withTimeStamps();
    }
    public function contributions(){
        return $this->hasMany(Contribution::class);
    }
}
