<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    // Nama tabel di database Anda
    protected $table = 'stock_mutations';

    protected $fillable = [
        'product_id',
        'quantity',
        'type', // 'in' atau 'out'
        'reference'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}