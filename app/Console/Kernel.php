<?php

namespace App\Console;

use App\Mail\LeaveReviewMail;
use DateTimeZone;
use App\Models\Order;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $now = date('Y-m-d H:i:s');
            $monthAgo = date_create($now);
            date_modify($monthAgo, '-32 days');
            $monthAgo = date_format($monthAgo, 'Y-m-d H:i:s');

            $orders = Order::where([['dispatched', '=', false], ['created_at', '>', $monthAgo]])->get();
            foreach($orders as $order){
                $ticket = $order->tickets[0];
                
                $tz = $order->timezone;
                $timestamp = time();
                $dt = new DateTime("now", new DateTimeZone($tz));
                $dt->setTimestamp($timestamp);
                if($ticket->dispatchDate < $now && $order->user->email){
                    Mail::to($order->user->email)->bcc(env('TICKETS_MAIL'))->send(new LeaveReviewMail($ticket));
                    
                    $order->dispatched = true;
                    $order->save();
                    Log::info('Отзыв предложен!');
                }
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}