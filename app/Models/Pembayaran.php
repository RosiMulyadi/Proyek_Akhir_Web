<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = "pembayaran";
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_bayar',
        'id_penyewa',
        'id_toko',
        'nama_penyewa',
        'harga',
        'bayar',
        'keterangan',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'name', 'nama_penyewa');
    }

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'id_penyewa');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'id_toko', 'harga');
    }
}
