<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login(Request $request){


        // if(!Auth::attempt(['phone' => '+7 (777) 777 7777', 'password' => $request->password])){
        $user = User::where([['email', '=', $request->email], ['role_id', '<>', 1]])->first();
        
        if(!$user){
            return response(['message' => 'Неверный пароль!'], 422);
        }
        Auth::login($user);
        // $token = Auth::user()->createToken('authToken')->accessToken;
        $request->session()->regenerate();
        // return response([
        //     'token' => $token
        // ]);
    }

    public function get(){
        return response(['user' => Auth::with('role')->user()]);
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}