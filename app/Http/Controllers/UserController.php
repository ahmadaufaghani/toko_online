<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
   public function update(Request $request, $id) {
        $user_profile = User::find($id);
        $user_profile->name = ($request->name) ? $request->name : $user_profile->name;
        $user_profile->email = ($request->email) ? $request->email : $user_profile->email;
        $user_profile->address = ($request->address) ? $request->address : $user_profile->address;
        $user_profile->update();
        $user_profile->save();

        return response()->json([
            'status'=>200,
            'message'=>'Data berhasil diperbarui!',
            'data'=>$user_profile
        ], 201);
   }
}
