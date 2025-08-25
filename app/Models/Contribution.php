<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = ['collaborate_id', 'user_id', 'amount', 'receipt_path'];
    
    public function collaborate(){
        return $this->belongsTo(Collaborate::class); 
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
