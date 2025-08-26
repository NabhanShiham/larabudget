<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Collaborate extends Model
{
    use HasFactory;
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
