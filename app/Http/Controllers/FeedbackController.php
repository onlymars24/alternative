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

        Mail::to(env('MAIL_FEEDBACK1'))->send(new FeedbackMail($request->name, $request->phone, $request->email, $request->topic, $request->descr));
        return response([
            'feedback' => $feedback
        ]);
    }

    public function get(){
        $feedback = Feedback::orderByDesc('id')->get();
        return response([
            'feedback' => $feedback
        ]);
    }

    // POST
    // id
    // comment
    public function editComment(Request $request){
        $feedback = Feedback::find($request->id);
        $feedback->comment = $request->comment;
        $feedback->save();
        return response([
            'feedback' => $feedback,
        ]);
    }

    // POST
    // id
    // status
    public function editStatus(Request $request){
        $feedback = Feedback::find($request->id);
        if($feedback->status != $request->status){
            $history = json_decode($feedback->history);
            $history[] = ['date' => date('Y-m-d H:i:s'), 'status' => $request->status ];
        }
        $feedback->status = $request->status;
        $feedback->save();
        return response([
            'feedback' => $feedback,
        ]);
    }
}