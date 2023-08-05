<?php

namespace App\Http\Controllers\Api;

use App\Models\Sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SendSmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $user = $request->user;
        $validator = Validator::make($user, [
            'phone' => 'required|size:17|unique:users',
            'password' => 'required|between:7,30|confirmed',
            'formConditionTop' => 'accepted',
            'formConditionBottom' => 'accepted',
        ]);
        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }
        $sms = Sms::create([
            'phone' => $user['phone'],
            'code' => random_int(100000, 999999),
            'user' => json_encode($user),
            'type' => $request->type
        ]);
        return json_encode($sms);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($phone, Request $request)
    {
        $sms = Sms::where([
            ['phone', '=', $phone],
            // ['code', '=', $request->code],
            ['type', '=', $request->type]
        ])->first();
        return json_encode($sms);
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
}