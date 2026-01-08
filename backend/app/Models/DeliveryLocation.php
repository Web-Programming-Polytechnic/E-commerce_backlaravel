<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryLocation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'delivery_id',
        'latitude',
        'longitude',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
