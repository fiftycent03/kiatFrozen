<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    // Field stok dihapus atas permintaan.
    protected $fillable = ['product_id', 'label', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
