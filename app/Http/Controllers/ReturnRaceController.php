<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Mail\RacesExistingMail;
use App\Models\CacheArrivalPoint;
use Illuminate\Support\Facades\Mail;

class ReturnRaceController extends Controller
{
    // status(Успешная/Неудачная)
    // dispatchName
    // arrivalName
    // orderId
    public function sendReturnRace(Request $request){
        $order = Order::find($request->orderId);
        Mail::to(env('RACES_EXISTING_MAIL'))->send(new RacesExistingMail($request->status, $request->dispatchName.' - '.$request->arrivalName, 
        $order->id, $order->user->phone, false));

        
        // $dispatchPoint = DispatchPoint::find($order->dispatchPointId);
        // $arrivalPoint = CacheArrivalPoint::where([['arrival_point_id', '=', $order->arrivalPointId], ['dispatch_point_id', '=', $order->dispatchPointId]])->first();
        // // return response([
        // //     'dispatchKladrSlug' => $dispatchPoint,
        // //     'arrivalKladrSlug' => $arrivalPoint
        // // ]);
        // if(!isset($dispatchPoint->kladr->arrivalPoints) || count($dispatchPoint->kladr->arrivalPoints) == 0 || !isset($arrivalPoint->kladr->dispatchPoints) || count($arrivalPoint->kladr->dispatchPoints) == 0){
        //     Mail::to(env('RACES_EXISTING_MAIL'))->send(new RacesExistingMail('Неудачная', $arrivalPoint->name.' - '.$dispatchPoint->name, 
        //     $request->orderId, $order->user->phone, false));
        //     return response([
        //         'dispatchKladrSlug' => null,
        //         'arrivalKladrSlug' => null
        //     ]);
        // }
        // Mail::to(env('RACES_EXISTING_MAIL'))->send(new RacesExistingMail('Успешная', $arrivalPoint->kladr->name.' - '.$dispatchPoint->kladr->name, 
        // $request->orderId, $order->user->phone, false));
        // return response([
        //     'dispatchKladrSlug' => $dispatchPoint->kladr->slug,
        //     'arrivalKladrSlug' => $arrivalPoint->kladr->slug
        // ]);
    }
}