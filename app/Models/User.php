<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'no_ktp',
        'email',
        'password',
        'alamat',
        'telepon',
        'jenkel',
        'tgl_lahir',
        'tmpt_lahir',
        'created_by', // Tambahkan created_by di sini
        'updated_by',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id'); // Gunakan 'role_id' sebagai foreign key
    }

    public function pemilik()
    {
        return $this->hasMany(Pemilik::class, 'name', 'alamat', 'telepon');
    }

    public function penyewa()
    {
        return $this->hasMany(Penyewa::class, 'no_ktp', 'name', 'alamat', 'telepon');
    }
}
