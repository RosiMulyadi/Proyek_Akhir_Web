<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = "stores";
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'id_pemilik',
        'id_toko',
        'gambar',
        'alamat',
        'luas_bangunan',
        'cluster',
        'harga',
        'created_by',
        'updated_by'
    ]; 
    
    /**
     * Definisi relasi dengan model Pemilik.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class, 'id_pemilik');
    }
}
