<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'description',
        'transaction_date',
        'receipt_path'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function user(){
        return $this.belongsTo(User::class);
    }
    public function category(){
        return $this.belongsTo(Category::class);
    }
}
