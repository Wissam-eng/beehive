<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class services extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'img',
        'payment_way',
        'cost',
        'description',
    ];
}
