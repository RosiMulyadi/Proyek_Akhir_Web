<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; 
    protected $fillable = [
        'id_toko',
        'gambar',
        'alamat',
        'luas_bangunan',
        'cluster',
        'harga',
        'created_by',
        'updated_by'
    ];    
}
