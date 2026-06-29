<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'qty', 'subtotal', 
        'name_snapshot', 'price_per_kg_snapshot', 'grams'
    ];

    public function product() { return $this->belongsTo(Product::class); }
}