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
    
    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class, 'user_id', 'id_pemilik');
    }
}
