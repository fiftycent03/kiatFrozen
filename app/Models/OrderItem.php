<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'variant_id', 'qty', 'subtotal',
        'name_snapshot', 'variant_label_snapshot', 'price_per_kg_snapshot', 'grams'
    ];

    public function product() { return $this->belongsTo(Product::class); }

    // Varian yang dibeli (null untuk produk unit_type='pcs' atau produk lama
    // sebelum fitur varian ada). nullOnDelete di migration menjaga baris ini
    // tetap ada walau varian aslinya sudah dihapus Admin.
    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}