<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'user_id', 
        'order_number', 
        'status', 
        'subtotal', 
        'shipping_cost', 
        'total', 
        'payment_method', 
        'payment_id', 
        'tracking_number', 
        'shipping_carrier', 
        'shipped_at', 
        'shipping_address',
        'tracking_number',
        'shipping_carrier',
        'shipped_at'
        ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
