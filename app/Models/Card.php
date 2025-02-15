<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card  extends Model
{
    use HasFactory;

    protected $fillable = [
        'sum_id',
        'segment',
        'cod',
        'credit',
        'entry',
        'debt',
        'tax',
        'deadline',
        'installment',
        'insurance',
        'adm',
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

    public function Sum(){
        return $this->belongsTo(Sum::class, 'sum_id', 'id');
    }
}
