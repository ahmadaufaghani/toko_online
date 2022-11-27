<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request) {
        $cek_user = User::count();
        if($cek_user == 0) {
            $user_datas = $request->validate([
                'name'=>'required',
                'email'=>'required|string|unique:users,email',
                'password'=>'required|min:6|confirmed'
            ]);
    
            $user = User::create([
                'name'=>$user_datas['name'],
                'email'=>$user_datas['email'],
                'password'=>Hash::make($user_datas['password']),
                'status'=>1
            ]);
    
            $token = $user->createToken('tokenku')->plainTextToken;
    
            return response()->json([
                'users'=>$user,
                'token'=>$token
            ], 201);
        } else {
            $user_datas = $request->validate([
                'name'=>'required',
                'email'=>'required|string|unique:users,email',
                'password'=>'required|min:6|confirmed'
            ]);
    
            $user = User::create([
                'name'=>$user_datas['name'],
                'email'=>$user_datas['email'],
                'password'=>Hash::make($user_datas['password']),
                'status'=>0
            ]);
    
            $token = $user->createToken('tokenku')->plainTextToken;
    
            return response()->json([
                'users'=>$user,
                'token'=>$token
            ], 201);
        }
    }

    public function login(Request $request) {
        $user_data = $request->validate([
            'email'=>'required|string',
            'password'=>'required|string',
        ]);

        $user = User::where('email',$user_data['email'])->first();

        if(!$user || Hash::check($user_data['password'],$user->password)) {
            $token = $user->createToken('tokenku')->plainTextToken;
            $response = [
                'user'=>$user,
                'token'=>$token
            ];
            return response($response, 201);
        } else {
            $response = [
                'message'=>'Kredensial anda salah!'
            ];
            return response($response, 404);
        }

    }
    
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return [
            'message'=>'Logged Out'
        ];
    }

    public function reset_password(Request $request, $id) {
        $reset_password = User::find($id);
        $current_password = $request->current_password;
        $password_validasi = $request->validate([
            'current_password'=>'required|min:6',
            'password'=>'required|min:6',
            'password_confirmation'=>'required|min:6|same:password'
        ]);

        if(Hash::check($password_validasi['current_password'], $reset_password->password)) {
            $reset_password->password = Hash::make($password_validasi['password']);
            $reset_password->update();
            $reset_password->save();
        } else {
            throw ValidationException::withMessages([
                "current_password" => "Kata sandi tidak sesuai dengan database kami."
            ]);
        }

        return response()->json([
            'message'=>'Kata sandi berhasil diubah.'
        ], 201);
    }
}
