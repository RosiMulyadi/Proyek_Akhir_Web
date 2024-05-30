<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemilik extends Model
{
    use HasFactory;

    protected $table = "pemilik";
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_pemilik',
        'name',
        'alamat',
        'telepon',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_pemilik', 'id');
    }

    public function store()
    {
        return $this->hasMany(Store::class, 'id_pemilik');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
