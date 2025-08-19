<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    use HasFactory;
    // TODO: change current spent to the sum of the amount col in purchases (the thing works on the frontend bc of a hack but this is poor quality)
    protected $fillable = [
        'user_id',  
        'mainbudget',
        'currentspent'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
