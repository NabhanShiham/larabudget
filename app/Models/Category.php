<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Category extends Model
{
    use HasFactory;

    public function purchases(){
        return $this->hasMany(Purchase::class);
    }

    protected static function booted() {
        static::updated(function ($category){
            $category->update([
                'current_spent' => $category->purchases()->sum('amount')
            ]);
        });
    }

    protected $fillable = [
        'name',  
        'budgeted_amount',
        'current_spent'
    ];
    public function receipts(){
        return $this->hasMany(Receipt::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
