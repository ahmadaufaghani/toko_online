<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produk = Produk::all();
        $produk_jumlah = Produk::count();
        if($produk_jumlah > 0) {
            return response()->json([
                'status'=>201,
                'message'=>'Data produk berhasil ditampilkan!',
                'data'=>$produk
            ],201);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Data produk tidak ditemukan!',
                'data'=>$produk
            ],404);
        }
        
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
        $user = $request->user();
        if($user && $user->status == 1) {
            $data = $request->validate([
                'nama'=>'required',
                'deskripsi'=>'required',
                'merk'=>'required',
                'warna'=>'required',
                'ukuran'=>'required',
                'harga'=>'required',
                'stok'=>'required',
                'gambar'=>'required|file|max:1024',
            ]);
    
            $nama_gambar = $data['gambar'];
            $nama_file = time().rand(100,999).".".$nama_gambar->getClientOriginalName();
    
            if($request->file('gambar')) {
                $data['gambar'] = $nama_file;
                $request->file('gambar')->move('admin/img/produk/',$nama_file);
            }

            $produk = Produk::create($data);
            return response()->json([
                'status'=>200,
                'message'=>'Data produk berhasil ditambahkan!',
                'data'=>$produk
            ],200);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Unathorized!'
            ],404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $produk = Produk::find($id);
        if($produk) {
            return response()->json([
                'status'=>201,
                'message'=>'Data produk berhasil ditemukan!',
                'data'=>$produk,
            ],201);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Data produk tidak ditemukan!',
                'data'=>[]
            ],404);
        }
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
        $user = $request->user();
        if($user && $user->status == 1) {
            $produk = Produk::find($id);
            if ($produk) {
                $produk->nama = $request->nama ? $request->nama : $produk->nama;
                $produk->deskripsi =  $request->deskripsi ? $request->deskripsi :  $produk->deskripsi;
                $produk->merk = $request->merk ? $request->merk : $produk->merk;
                $produk->warna = $request->warna ? $request->warna : $produk->warna;
                $produk->ukuran = $request->ukuran ? $request->ukuran : $produk->ukuran;
                $produk->harga = $request->harga ? $request->harga : $produk->harga;
                $produk->stok = $request->stok ? $request->stok : $produk->stok;
                
                $nama_gambar = $produk->gambar;
                if ($request->file('gambar')) {
                    $produk->gambar = $nama_gambar;
                    $request->file('gambar')->move('admin/img/produk/',$nama_gambar);
                } else {
                    $produk->gambar = $nama_gambar;
                }
                $produk->update();
        
                return response()->json([
                    'status'=>200,
                    'message'=>'Data produk berhasil diperbarui!',
                    'data'=>$produk
                ],200);
            } else {
                return response()->json([
                    'status'=>404,
                    'message'=>'Data produk tidak ditemukan!',
                    'data'=>[]
                ],404);
            }
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Unathorized!'
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
        $user = Auth::user();
        if($user && $user->status == 1) {
            $produk = Produk::find($id);
            if($produk){
                $path = public_path('admin/img/produk/').$produk->gambar;
                if(file_exists($path)) {
                    @unlink($path);
                }
                $produk->delete();
                return response()->json([
                    'status'=>200,
                    'message'=>'Data produk berhasil dihapus!',
                    'data'=>$produk,
                ], 200);
            } else {
                return response()->json([
                    'status'=>400,
                    'message'=>'Data produk tidak ditemukan!',
                    'data'=>[],
                ], 400);
            }
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Unathorized!'
            ],404);
        }
    }

    public function search($name) {
        $produk = Produk::where('nama','like', '%'.$name.'%')->get();
        $produk_jumlah = Produk::where('nama','like', '%'.$name.'%')->count();
        if($produk_jumlah > 0) {
            return response()->json([
                'status'=>201,
                'message'=>'Data produk berhasil ditemukan!',
                'data'=>$produk
            ], 201);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Data produk tidak ditemukan!',
                'data'=>[]
            ], 404);
        }
    }
}
