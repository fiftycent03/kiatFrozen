<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Kolom yang boleh diisi massal (termasuk 'image' untuk gambar kategori baru).
    protected $fillable = ['name', 'slug', 'is_active', 'image'];

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }
}
