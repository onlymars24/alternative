<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpMember;
use Nette\Utils\DateTime;
use App\Mail\OtpMemberMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;

class MemberAuthController extends Controller
{
    public function sendCode(Request $request){
        $user = User::where([['email', '=', $request->email]])->whereHas('role', function(Builder $query){
            return $query->where([['name', '<>', 'Пользователь']]);
        })->first();
        if(!$user){
            return response(['errorMessage' => 'Соотрудник с данным адресом не был найден'], 422);
        }
        
        $code = random_int(100000, 999999);
        $date = new DateTime(); // Текущее время
        $date->modify('+5 minutes'); // Прибавляем 5 минут
        $optMember = OtpMember::create([
            'code' => Hash::make($code),
            'user_id' => $user->id,
            'expired_at' => $date->format('Y-m-d H:i:s')
        ]);
        Mail::to($user->email)->send(new OtpMemberMail($code));
    }

    public function confirmCode(Request $request){
        // return response(['code' => $request->code]); 
        $hashedCode = Hash::make($request->code);
        $user = User::where([['email', '=', $request->email]])->whereHas('role', function(Builder $query){
            return $query->where([['name', '<>', 'Пользователь']]);
        })->first();
        
        $otpMember = null;
        foreach($user->otps as $otp){
            if(Hash::check($request->code, $otp->code)){
                $otpMember = $otp;
                break;
            }
        }
        if(!$otpMember){
            return response(['errorMessage' => 'Код подтверждения неверный'], 422);
        }
        if($otpMember->expired_at < date('Y-m-d H:i:s')){
            // $otpMember->delete();
            return response(['errorMessage' => 'Время действия кода истекло'], 422);
        }
        $otpMember->delete();
        // $user = $otpMember->user;
        Auth::login($user);
        $request->session()->regenerate();
    }

    public function member(){
        return response(['member' => Auth::user()]);
    }

    public function logout(Request $request){
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}