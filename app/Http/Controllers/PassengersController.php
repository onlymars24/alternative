<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PassengersController extends Controller
{
    public function all(){
        $user = Auth::user();
        $passengers = $user->passengers;
        return response([
            'passengers' => $passengers
        ]);
    }

    public function delete(Request $request){
        $user = Auth::user();
        $passenger = $user->passengers->find($request->passengerId);
        if(!$passenger){
            return response([
                'error' => 'Пассажир не найден!'
            ], 404);
        }
        $passenger->delete();
        return response([
            'message' => 'Пассажир успешно удалён!'
        ]);
    }

    public function edit(Request $request){
        $validator = Validator::make($request->all(), [
            'surname' => 'required',
            'name' => 'required',
            'patronymic' => 'required',
            'birth_date' => 'required',
            'citizenship' => 'required',
            'doc_number' => 'required',
            'doc_series' => 'required',
            'gender' => 'required',
        ]);
        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }
        $passenger = Passenger::find($request->id);
        $passenger->update($request->except('errors', 'openInputs'));
        $passenger->save();
    }

    public function save(Request $request){
        $validator = Validator::make($request->all(), [
            'surname' => 'required',
            'name' => 'required',
            'patronymic' => 'required',
            'birth_date' => 'required',
            'citizenship' => 'required',
            'doc_number' => 'required',
            'doc_series' => 'required',
            'gender' => 'required',
        ]);
        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }
        $passenger = Passenger::create([
            'name' => $request->name,
            'patronymic' => $request->patronymic,
            'birth_date' => $request->birth_date,
            'citizenship' => $request->citizenship,
            'doc_number' => $request->doc_number,
            'doc_series' => $request->doc_series,
            'doc_type' => $request->doc_type,
            'ticket_type' => $request->ticket_type,
            
        ]);
        return response([
            'passenger' => $passenger
        ]);
    }
}