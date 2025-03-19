<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clients_cancel extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'bank_number',
        'mobile_wallet',
        'name',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(clients::class, 'client_id', 'id');
    }
}
