<?php

namespace App\Http\Controllers;

use App\Mail\RacesExistingMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RacesExistingMailController extends Controller
{
    public function send(Request $request){
        Mail::to(env('RACES_EXISTING_MAIL'))->send(new RacesExistingMail($request->status, $request->points));

    }
}
