<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'sku_root',
        'price_per_kg',
        'min_pembelian',
        'satuan',
        'description',
        'is_active',
        'stock', 
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    // Tambahan relasi riwayat stok
    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
    }
}