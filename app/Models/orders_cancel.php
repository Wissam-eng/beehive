<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_cancel extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'client_id',
        'bank_number',
        'mobile_wallet',
        'cancel_reason',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(clients::class, 'client_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(services_client::class, 'order_id', 'id');
    }
}
