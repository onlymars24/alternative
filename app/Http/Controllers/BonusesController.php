<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusesController extends Controller
{
    public function transactions(){
        $bonuses = Bonus::all();
        return response([
            'bonuses' => $bonuses
        ]);
    }

    public function plus(Request $request){
        $user = User::find($request->id);
        $user->bonuses_balance = $user->bonuses_balance + $request->bonuses;
        $user->save();
        $bonuse = Bonus::create([
            'amount' => $request->bonuses,
            'transaction' => 'plus',
            'user_id' => $user->id,
            'user_phone' => $user->phone,
            'descr' => $request->descr
        ]);
    }

    public function minus(Request $request){
        $user = User::find($request->id);
        if($user->bonuses_balance >= $request->bonuses){
            $user->bonuses_balance = $user->bonuses_balance - $request->bonuses;
            $user->save();
            $bonuse = Bonus::create([
                'amount' => $request->bonuses,
                'transaction' => 'minus',
                'user_id' => $user->id,
                'user_phone' => $user->phone,
                'descr' => $request->descr
            ]);
        }
    }

    public function user(Request $request){
        $user = User::find($request->id);
        return response([
            'bonuses' => $user->bonuses
        ]);
    }
}