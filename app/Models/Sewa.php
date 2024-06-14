<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sewa extends Model
{
    use HasFactory;

    protected $table = "sewa";
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'id_pemilik',
        'id_penyewa',
        'id_toko',
        'gambar',
        'alamat',
        'luas_bangunan',
        'cluster',
        'harga',
        'created_by',
        'updated_by'
    ];

    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class, 'id_pemilik');
    }

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'id_penyewa');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'id_toko', 'gambar', 'alamat', 'luas_bangunan', 'harga');
    }
}
