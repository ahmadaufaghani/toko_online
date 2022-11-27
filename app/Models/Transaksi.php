<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksis';
    protected $fillable = [
        'keranjang_id',
        'pengguna_id',
        'status_pembayaran',
        'konfirmasi_pembelian',
        'konfirmasi_sampai',
        'metode',
        'jenis',
        'kode_va',
        'jumlah_harga'
    ];
}
