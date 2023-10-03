<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = "penyewa";
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_penyewa',
        'nama',
        'no_ktp',
        'alamat',
        'telepon',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'no_ktp', 'nama', 'alamat', 'telepon');
    }
}
