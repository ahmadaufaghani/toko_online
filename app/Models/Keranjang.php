<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;
    protected $table = "keranjangs";
    protected $fillable = [
        'produk_id',
        'pengguna_id',
        'kuantitas',
        'deskripsi'
    ];
}
