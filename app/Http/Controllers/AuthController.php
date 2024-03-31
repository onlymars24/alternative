<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use App\Mail\OrderMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\RegisterReset\CodeStatusMail;

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

        Auth::loginUsingId($user->id);
        $token = Auth::user()->createToken('authToken')->accessToken;
        Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail($request->phone, 'Регистрация', true));
        return response([
            'token' => $token
        ]);
        // return response()->json(['success' => true, 'message' => 'Registration is succeeded'], 200);
    }

    public function login(Request $request){
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
        
        Auth::loginUsingId($user->id);
        $token = Auth::user()->createToken('authToken')->accessToken;
        Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail($request->phone, 'Смена пароля', true));
        return response([
            'token' => $token
        ]);

        // return response([
        //     'sms' => $sms
        // ]);
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

        $order = $user->orders->last();

        if($user->email && json_decode($order->order_info)->status == 'S'){
            Mail::to($user->email)->bcc(env('TICKETS_MAIL'))->send(new OrderMail($order->tickets));
        }

        
        return response([
            'success' => true
        ]);
    }

    public function users(){
        return response([
            'users' => User::all()
        ]);
    }
}