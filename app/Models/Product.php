<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Field stok dihapus atas permintaan — ketersediaan beli kini murni mengikuti `is_active`.
    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'sku_root',
        'price_per_kg',
        'min_pembelian',
        'satuan',
        'unit_type',
        'description',
        'is_active',
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

    // Relasi ke Varian Potongan/Gramasi (HANYA relevan untuk unit_type='kg').
    // Produk unit_type='pcs' TIDAK memiliki baris di sini sama sekali —
    // harga & stoknya cukup dibaca langsung dari price_per_kg/stock di atas.
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Helper Pcs/Kg — dipakai di Blade (Form Admin & Detail Produk) & Controller
    // agar logika If/Else tidak mengulang string 'pcs'/'kg' di banyak tempat.
    public function isKg(): bool
    {
        return $this->unit_type === 'kg';
    }

    public function isPcs(): bool
    {
        return $this->unit_type !== 'kg'; // default aman: apa pun selain 'kg' dianggap Pcs
    }
}