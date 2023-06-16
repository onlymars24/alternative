<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login(Request $request){
        if(!Auth::attempt(['phone' => '+7 (777) 777 7777', 'password' => $request->password])){
            return response(['message' => 'Неверный пароль!'], 422);
        }

        $token = Auth::user()->createToken('authToken')->accessToken;

        return response([
            'token' => $token
        ]);
    }
}
