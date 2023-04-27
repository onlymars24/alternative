<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|size:17',
            'password' => 'required|between:6,30|confirmed'
        ]);

        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }
        $user = User::create([
            'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);
        
        if(!$user){
            return response()->json(['success' => false, 'message' => 'Registration is failed'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Registration is succeeded'], 200);;
    }

    public function login(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'phone' => 'required',
        //     'password' => 'required'
        // ]);
        // if($validator->fails()){
        //     return response(
        //         [
        //             'errors' => $validator->errors()
        //         ], 422
        //     );
        // }
        if(!Auth::attempt($request->all())){
            return response(['message' => 'Неверный номер или пароль!'], 422);
        }

        $token = Auth::user()->createToken('authToken')->accessToken;

        return response([
            'token' => $token
        ]);
    }

    public function reset(Request $request){
        $user = User::where('phone', $request->phone)->first();
        if(!$user){
            return response([
                'error' => 'Пользователя с таким номером не существует!'
            ], 422);
        }
        $sms = Sms::where([
            ['phone', '=', $request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'reset']
        ])->first();
        if(!$sms){
            return response([
                'error' => 'Код неверный!'
            ], 422);
        }
        $validator = Validator::make($request->all(), [
            'password' => 'required|between:6,30|confirmed'
        ]);

        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }
        $user = User::where('phone', $request->phone)->first();
        foreach($user->tokens as $token) {
            $token->revoke();   
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return response([
            'sms' => $sms
        ]);
    }

    public function user(){
        return response([
            'user' => Auth::user()
        ]);
    }

    public function editEmail(Request $request){
        $user = Auth::user();
        $user->email = $request->email;
        $user->save();
        return response([
            'success' => true
        ]);
    }
}