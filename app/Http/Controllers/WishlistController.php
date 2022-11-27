<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wishlist = Wishlist::join('produks','produks.id','=','wishlists.produk_id')
                            ->join('users','users.id','=', 'wishlists.pengguna_id')
                            ->select('produks.*','users.id AS user_id','wishlists.created_at', 'wishlists.updated_at')
                            ->where('users.id',Auth::user()->id)
                            ->get();
        return response()->json([
            'status'=>201,
            'message'=>'Data wishlist berhasil ditemukan!',
            'data'=>$wishlist
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
        $wishlist = Wishlist::create([
            'produk_id'=>$request->produk_id,
            'pengguna_id'=>Auth::user()->id
        ]);

        $wishlist_data = Wishlist::join('produks','produks.id','=','wishlists.produk_id')
                                 ->join('users','users.id','=','wishlists.pengguna_id')
                                 ->select('produks.*','users.name','wishlists.created_at','wishlists.updated_at')
                                 ->where('wishlists.pengguna_id', Auth::user()->id)
                                 ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Data wishlist berhasil ditambahkan!',
            'data' => $wishlist_data
        ], 200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $wishlist = Wishlist::find($id);
        if($wishlist) {
            $wishlist->delete();
            return response()->json([
                'status'=>201,
                'message'=>'Data wishlist berhasil dihapus!',
                'data'=>[]
            ], 201);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Data wishlist tidak ditemukan!',
                'data'=>[],
            ], 404);
        }
    }

    public function search_wishlist($name) {
        $wishlist_data = Wishlist::join('produks','produks.id','=','wishlists.produk_id')
                                 ->join('users','users.id','=','wishlists.pengguna_id')
                                 ->select('produks.*','users.name','wishlists.created_at','wishlists.updated_at')
                                 ->where('wishlists.pengguna_id', Auth::id())
                                 ->where('produks.nama','like','%'.$name.'%')
                                 ->get();
        $wishlist_jumlah = Wishlist::join('produks','produks.id','=','wishlists.produk_id')
                                    ->where('produks.nama','like','%'.$name.'%')->count();
        if($wishlist_jumlah > 0) {
            return response()->json([
                'status'=>201,
                'message'=>'Data wishlist berhasil ditemukan!',
                'data'=>$wishlist_data
            ],201);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Data wishlist tidak ditemukan!',
                'data'=>[]
            ],404);
        }
    }
}
