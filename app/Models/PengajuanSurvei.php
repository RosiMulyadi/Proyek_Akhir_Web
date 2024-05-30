<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSurvei extends Model
{
    use HasFactory;

    protected $table = "pengajuan_survei";
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_penyewa',
        'nama_penyewa',
        'no_ktp',
        'tanggal_survei',
        'waktu',
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
}
