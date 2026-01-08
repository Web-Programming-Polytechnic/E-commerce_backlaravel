<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_address',
        'amount',
        'instructions',
        'status',
    ];

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
}
