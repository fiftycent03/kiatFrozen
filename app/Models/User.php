<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 'role' sengaja tetap fillable agar controller bisa meng-set-nya lewat mass assignment,
    // TAPI nilainya tidak boleh pernah diambil langsung dari input request pengguna
    // (mis. $request->all() atau $request->role). Controller wajib hardcode nilai role
    // ('admin'/'kurir'/'user') secara eksplisit — lihat UserController@storeCourier —
    // supaya user tidak bisa self-assign role admin/kurir lewat form tampering.
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // HAPUS AUTO HASH
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function addresses()
{
    return $this->hasMany(UserAddress::class);
}
}
