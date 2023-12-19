<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

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
