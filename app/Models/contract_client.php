<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contract_client extends Model
{
    use HasFactory;

    protected $fillable = [
        'signature_client',
        'client_id',
        'contract_id',
    ];
}
