<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sum  extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credit',
        'entry',
        'debt',
        'tax',
        'deadline',
        'installment',
        'insurance',
        'fund',
    ];

    protected $casts = [
        'credit' => 'double',
        'entry' => 'double',
        'debt' => 'double',
        'tax' => 'double',
        'deadline' => 'double',
        'installment' => 'double',
        'insurance' => 'double',
        'fund' => 'double',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cards(){
        return $this->hasMany(Card::class, 'sum_id');
    }
}
