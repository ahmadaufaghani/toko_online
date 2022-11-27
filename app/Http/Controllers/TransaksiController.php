<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\User;
use DB;
use Auth;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if($user && $user->status == 1) {
            $transaksi = Transaksi::select('id', 'pengguna_id','status_pembayaran','konfirmasi_pembelian','konfirmasi_sampai','jumlah_harga','metode','jenis','kode_va','created_at','updated_at')
                                ->get();
            return response()->json([
                'status'=>201,
                'message'=>'Data berhasil ditampilkan',
                'data'=>$transaksi
            ]);
        } else {
            $transaksi = Transaksi::select('id', 'metode','jenis', 'status_pembayaran','konfirmasi_pembelian','konfirmasi_sampai','jumlah_harga','kode_va','created_at','updated_at')
                                ->where('pengguna_id',Auth::user()->id)
                                ->get();
            return response()->json([
                'status'=>201,
                'message'=>'Data berhasil ditampilkan',
                'data'=>$transaksi
            ]);
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
        $user = Auth::user();
        if($user && $user->status == 0) {
            $kode_va = '800'.time();
            $data_transaksi = $request->validate([
                'pengguna_id'=>'required',
                'metode'=>'required',
                'jenis'=>'required',
                'jumlah_harga'=>'required'
            ]);
     
            $data_transaksi['status_pembayaran'] = 'PENDING';
            $data_transaksi['konfirmasi_pembelian'] = 'PENDING';
            $data_transaksi['konfirmasi_sampai'] = 'ONGOING';
            $data_transaksi['kode_va'] = $kode_va;

            $transaksi = Transaksi::create($data_transaksi);

            return response()->json([
                'status'=>200,
                'message'=>'Data berhasil ditambahkan!',
                'data'=>$transaksi
            ]);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Unauthorized!',
                'data'=>[]
            ]);
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
    public function destroy($id)
    {
        //
    }

    public function pay_transactions(Request $request) {
        $user = Auth::user();
        if($user && $user->status == 0) {
            $data_transaksi = Transaksi::where('kode_va',$request->kode_va)
                                        ->get();
            $data_transaksi_jumlah = Transaksi::where('kode_va',$request->kode_va)
                                        ->count();
            if($data_transaksi_jumlah > 0) {
                $data_transaksi_update = Transaksi::where('kode_va',$request->kode_va)
                                        ->update(array('status_pembayaran'=>'SUCCESS'));
                return response()->json([
                    'status'=>200,
                    'message'=>'Pembayaran telah berhasil!',
                    'data'=>$data_transaksi
                ]);
            } else {
                return response()->json([
                    'status'=>404,
                    'message'=>'Kesalahan pada pembayaran!',
                    'data'=>[]
                ]);
            }
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Unauthorized!',
                'data'=>[]
            ]);
        }
    }

    public function konfirmasi_transaksi(Request $request, $id) {
        $user = Auth::user();
        if($user && $user->status == 1) {
            $data_transaksi = Transaksi::find($id);
            $data_transaksi_jumlah = Transaksi::where('id',$id)
                                        ->count();
            if($data_transaksi_jumlah > 0) {
                $data_transaksi->konfirmasi_pembelian = 'SETTLEMENT';
                $data_transaksi->update();
                return response()->json([
                    'status'=>200,
                    'message'=>'Konfirmasi telah berhasil!',
                    'data'=>$data_transaksi
                ]);
            } else {
                return response()->json([
                    'status'=>404,
                    'message'=>'Konfirmasi gagal!',
                    'data'=>[]
                ]);
            }
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Unauthorized!',
                'data'=>[]
            ]);
        }
    }

    public function konfirmasi_sampai(Request $request, $id) {
        $user = Auth::user();
        if($user && $user->status == 0) {
            $data_transaksi = Transaksi::find($id);
            $data_transaksi_jumlah = Transaksi::where('id',$id)
                                        ->count();
            if($data_transaksi_jumlah > 0) {
                $data_transaksi->konfirmasi_sampai = 'ARRIVED';
                return response()->json([
                    'status'=>200,
                    'message'=>'Konfirmasi telah berhasil!',
                    'data'=>$data_transaksi
                ]);
            } else {
                return response()->json([
                    'status'=>404,
                    'message'=>'Konfirmasi gagal!',
                    'data'=>[]
                ]);
            }
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'Unauthorized!',
                'data'=>[]
            ]);
        }
    }
}
