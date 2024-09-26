<?php

namespace App\Exceptions;

use Throwable;
use App\Mail\ExceptionOccuredMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $this->sendEmail($e);
        });
    }

        /**

     * Write code on Method

     *

     * @return response()

     */

     public function sendEmail(Throwable $exception)
     {
 
        try {
 
    
 
             $content['message'] = $exception->getMessage();
 
             $content['file'] = $exception->getFile();
 
             $content['line'] = $exception->getLine();
 
             $content['trace'] = $exception->getTrace();
 
   
 
             $content['url'] = request()->url();
 
             $content['body'] = request()->all();
 
             $content['ip'] = request()->ip();
 
            
            Log::info(json_encode($content));
            Mail::to('marsel.galimov.241@gmail.com')->send(new ExceptionOccuredMail($content));

         } catch (Throwable $exception) {
 
             Log::error($exception);
 
         }
 
     }
}
