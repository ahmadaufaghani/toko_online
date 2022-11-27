<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keranjang;
use Illuminate\Support\Facades\Auth;
use DB;


class KeranjangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keranjang_produk = Keranjang::join('produks','produks.id','=','keranjangs.produk_id')
                              ->join('users','users.id','=', 'keranjangs.pengguna_id')
                              ->select('produks.id','produks.nama','produks.deskripsi','produks.merk', 'produks.warna', 'produks.ukuran', 'produks.harga','produks.gambar','keranjangs.created_at', 'keranjangs.updated_at', 'keranjangs.deskripsi AS deskripsi_keranjang','keranjangs.kuantitas',DB::raw('(produks.harga * keranjangs.kuantitas) AS jumlah'))
                              ->where('users.id',Auth::user()->id)
                              ->get();

        $keranjang = Keranjang::join('produks','produks.id','=','keranjangs.produk_id')
                              ->join('users','users.id','=', 'keranjangs.pengguna_id')
                              ->select(DB::raw('SUM(produks.harga*keranjangs.kuantitas) AS total_harga'), DB::raw('SUM(keranjangs.kuantitas) AS total_kuantitas'), 'users.id AS pengguna_id')
                              ->where('users.id',Auth::user()->id)
                              ->groupBy('users.id')
                              ->get();

        return response()->json([
            'status'=>201,
            'message'=>'Data berhasil ditampilkan',
            'data'=>[
                'keranjang'=>$keranjang,
                'produk'=>$keranjang_produk
            ]
        ], 201);
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        
        
        $data = $request->validate([
            'produk_id'=>'required',
            'kuantitas'=>'required',
            'deskripsi'=>'required|string'
        ]);
        
        $keranjang = Keranjang::create([
            'produk_id'=>$data['produk_id'],
            'pengguna_id'=>Auth::user()->id,
            'kuantitas'=>$data['kuantitas'],
            'deskripsi'=>$data['deskripsi']
        ]);
        
        $keranjang = Keranjang::join('produks','produks.id','=','keranjangs.produk_id')
                              ->join('users','users.id','=', 'keranjangs.pengguna_id')
                              ->select(DB::raw('SUM(produks.harga) AS total_harga'), DB::raw('SUM(keranjangs.kuantitas) AS total_kuantitas'), 'users.id AS pengguna_id')
                              ->where('users.id',Auth::user()->id)
                              ->groupBy('users.id')
                              ->get();

        $keranjang_produk = Keranjang::join('produks','produks.id','=','keranjangs.produk_id')
                              ->join('users','users.id','=', 'keranjangs.pengguna_id')
                              ->select('produks.id','produks.nama','produks.deskripsi','produks.merk', 'produks.warna', 'produks.ukuran', 'produks.harga','produks.gambar','keranjangs.created_at', 'keranjangs.updated_at', 'keranjangs.deskripsi AS deskripsi_keranjang','keranjangs.kuantitas',DB::raw('(produks.harga * keranjangs.kuantitas) AS jumlah'))
                              ->where('users.id',Auth::user()->id)
                              ->get();

        return response()->json([
            'status'=>201,
            'message'=>'Data berhasil ditampilkan',
            'data'=>[
                'keranjang'=>$keranjang,
                'produk'=>$keranjang_produk
                ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $keranjang = Keranjang::find($id);
        if($keranjang) {
            $keranjang->kuantitas = $request->kuantitas ? $request->kuantitas : $keranjang->kuantitas;
            $keranjang->deskripsi = $request->deskripsi ? $request->deskripsi : $keranjang->deskripsi;
            $keranjang->update();
            $keranjang->save();
    
            $keranjang_detail = Keranjang::join('produks','produks.id','=','keranjangs.produk_id')
                                        ->join('users','users.id','=', 'keranjangs.pengguna_id')
                                        ->select('produks.id','produks.nama','produks.deskripsi','produks.merk', 'produks.warna', 'produks.ukuran', 'produks.harga','produks.gambar','keranjangs.created_at', 'keranjangs.updated_at', 'keranjangs.deskripsi AS deskripsi_keranjang','keranjangs.kuantitas',DB::raw('(produks.harga * keranjangs.kuantitas) AS jumlah'))
                                        ->where('keranjangs.id', $id)
                                        ->where('users.id',Auth::user()->id)
                                        ->get();
            return response()->json([
                'status'=>200,
                'message'=>'Data berhasil diperbarui!',
                'data'=>$keranjang_detail
            ],200);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Data tidak ditemukan!',
                'data'=>[]
            ],404);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keranjang = Keranjang::find($id);
        if($keranjang) {
            $keranjang->delete();
            return response()->json([
                'status'=>201,
                'message'=>'Data berhasil dihapus',
                'data'=>[]
            ], 201);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Data tidak ditemukan!',
                'data'=>[]
            ],404);
        }
    }

    public function search($name)
    {
        $keranjang = Keranjang::join('produks','produks.id','=','keranjangs.produk_id')
                                ->join('users','users.id','=', 'keranjangs.pengguna_id')
                                ->select('produks.id','produks.nama','produks.deskripsi','produks.merk', 'produks.warna', 'produks.ukuran', 'produks.harga','produks.gambar','keranjangs.created_at', 'keranjangs.updated_at', 'keranjangs.deskripsi AS deskripsi_keranjang','keranjangs.kuantitas',DB::raw('(produks.harga * keranjangs.kuantitas) AS jumlah'))
                                ->where('produks.nama','like','%'.$name.'%')
                                ->where('users.id',Auth::user()->id)
                                ->get();
        $keranjang_count = Keranjang::join('produks','produks.id','=','keranjangs.produk_id')
                                ->join('users','users.id','=', 'keranjangs.pengguna_id')
                                ->select('produks.id','produks.nama','produks.deskripsi','produks.merk', 'produks.warna', 'produks.ukuran', 'produks.harga','produks.gambar','keranjangs.created_at', 'keranjangs.updated_at', 'keranjangs.deskripsi AS deskripsi_keranjang','keranjangs.kuantitas',DB::raw('(produks.harga * keranjangs.kuantitas) AS jumlah'))
                                ->where('produks.nama','like','%'.$name.'%')
                                ->where('users.id',Auth::user()->id)
                                ->count();
        if($keranjang_count > 0) {
            return response()->json([
                'status'=>200,
                'message'=>'Data berhasil ditemukan',
                'data'=>$keranjang
            ], 201);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Data tidak ditemukan!',
                'data'=>[]
            ],404);
        }
    }
}
