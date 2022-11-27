<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\TransaksiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);
Route::get('/produk',[ProdukController::class, 'index']);
Route::get('/produk/{id}',[ProdukController::class, 'show']);
Route::get('/produk/search/{name}',[ProdukController::class, 'search']);
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('/logout',[AuthController::class, 'logout']);
    Route::put('/update_profile/{id}',[UserController::class, 'update']);
    Route::put('/reset_password/{id}',[AuthController::class, 'reset_password']);
    Route::post('/produk/admin',[ProdukController::class, 'store']);
    Route::put('/produk/admin/{id}',[ProdukController::class, 'update']);
    Route::delete('/produk/admin/{id}',[ProdukController::class, 'destroy']);
    Route::get('/wishlist',[WishlistController::class, 'index']);
    Route::post('/wishlist',[WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}',[WishlistController::class, 'destroy']);
    Route::get('/wishlist/{name}',[WishlistController::class, 'search_wishlist']);
    Route::get('/keranjang',[KeranjangController::class, 'index']);
    Route::post('/keranjang',[KeranjangController::class, 'store']);
    Route::put('/keranjang/{id}',[KeranjangController::class, 'update']);
    Route::delete('/keranjang/{id}',[KeranjangController::class, 'destroy']);
    Route::get('/keranjang/{name}',[KeranjangController::class, 'search']);
    Route::get('/transaksi',[TransaksiController::class, 'index']);
    Route::post('/transaksi',[TransaksiController::class, 'store']);
    Route::post('/bayar',[TransaksiController::class, 'pay_transactions']);
    Route::put('/konfirmasi_transaksi/{id}',[TransaksiController::class, 'konfirmasi_transaksi']);
    Route::put('/konfirmasi_sampai/{id}',[TransaksiController::class, 'konfirmasi_sampai']);
});
