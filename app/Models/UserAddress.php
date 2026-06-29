<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    // Daftarkan kolom yang boleh diisi secara massal
    protected $fillable = [
        'user_id',
        'label',
        'customer_name',
        'customer_phone',
        'province',
        'city',
        'district',
        'address_detail',
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}