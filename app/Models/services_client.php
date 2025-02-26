<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class services_client extends Model
{
    use HasFactory;


    protected $fillable = [
        'service_name',
        'service_cost',
        'client_id',
        'payment_status',
        'status',
        'order_id',
        'trans_id',
        'referenceNumber',
    ];
}
