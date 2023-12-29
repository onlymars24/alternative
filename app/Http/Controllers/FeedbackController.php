<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Mail\FeedbackMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function send(Request $request){
        $feedback = Feedback::create([
            'phone' => $request->phone,
            'name' => $request->name,
            'email' => $request->email,
            'topic' => $request->topic,
            'descr' => $request->descr,
        ]);

        Mail::to(env('MAIL_FEEDBACK'))->send(new FeedbackMail($request->name, $request->phone, $request->email, $request->topic, $request->descr));
        return response([
            'feedback' => $feedback
        ]);
    }

    public function get(){
        $feedback = Feedback::all();
        return response([
            'feedback' => $feedback
        ]);
    }
}