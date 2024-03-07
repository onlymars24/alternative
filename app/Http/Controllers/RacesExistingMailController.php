<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Mail\RacesExistingMail;
use Illuminate\Support\Facades\Mail;

class RacesExistingMailController extends Controller
{
    public function send(Request $request){
        $order = Order::find($request->orderId);
        $phone = $order->user->phone;
        Mail::to(env('RACES_EXISTING_MAIL'))->send(new RacesExistingMail($request->status, $request->points, $request->orderId, $phone));
    }
}
