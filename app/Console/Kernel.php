<?php

namespace App\Console;

use DateTimeZone;
use App\Models\Order;
use App\Models\CacheRace;
use Nette\Utils\DateTime;
use App\Models\WhatsAppSms;
use App\Services\SmsService;
use App\Mail\LeaveReviewMail;
use App\Services\KladrService;
use App\Models\CacheArrivalPoint;
use App\Services\ScheduleService;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
        // $schedule->call(function () {
        //     $now = date('Y-m-d H:i:s');
        //     $monthAgo = date_create($now);
        //     date_modify($monthAgo, '-32 days');
        //     $monthAgo = date_format($monthAgo, 'Y-m-d H:i:s');

        //     $orders = Order::where([['dispatched', '=', false], ['created_at', '>', $monthAgo]])->get();
        //     foreach($orders as $order){
        //         $ticket = $order->tickets[0];
                
        //         $tz = $order->timezone;
        //         $timestamp = time();
        //         $dt = new DateTime("now", new DateTimeZone($tz));
        //         $dt->setTimestamp($timestamp);
        //         if($ticket->dispatchDate < $now && $order->user && $order->user->email){
        //             Mail::to($order->user->email)->bcc(env('TICKETS_MAIL'))->send(new LeaveReviewMail($ticket));
                    
        //             $order->dispatched = true;
        //             $order->save();
        //             Log::info('Отзыв предложен! orderId: '.$order->id.'; ticketId: '.$ticket->id);
        //         }
        //     }
        // })->daily();
        $schedule->call(function () {
            $now = date('Y-m-d H:i:s');
            $currentOrdersTime = date_create($now);
            date_modify($currentOrdersTime, '-15 min');
            $currentOrdersTime = date_format($currentOrdersTime, 'Y-m-d H:i:s');
            $orders = Order::where([['created_at', '>', $currentOrdersTime], ['paymentInformed', '=', false]])->get();
            foreach($orders as $order){
                $createdAt = date_format($order->created_at, 'Y-m-d H:i:s');
                $orderPlus5min = date_create($createdAt);
                date_modify($orderPlus5min, '+5 min');
                $orderPlus5min = date_format($orderPlus5min, 'Y-m-d H:i:s');
                $orderObj = json_decode($order->order_info);
                if($orderPlus5min < $now && !$order->paymentInformed && $orderObj->status == 'B'){
                    $user = $order->user;
                    $phoneWithoutMask = SmsService::removeMask($user->phone);
                    $checkWhatsApp = Http::
                    post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
                    $checkWhatsApp = json_decode($checkWhatsApp);

                    if(isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
                        $message = 'Ваш забронированный билет ожидает оплаты.

На всякий случай дублируем ссылку на оплату:
'.$order->formUrl;
                        $whatsAppService = Http::
                        post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$message
                        .'&instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
                        $whatsAppService = json_decode($whatsAppService);
                        if(!isset($whatsAppService->data->task_id)){
                            Log::info('whatsAppService: '.json_encode($whatsAppService));
                            continue;
                        }
                        $whatsAppSms = WhatsAppSms::create([
                            'id' => $whatsAppService->data->task_id,
                            'phone' => $user->phone,
                            'type' => 'paymentReminder',
                            'status' => 0,
                            'message' => $message
                        ]);
                        $order->paymentInformed = true;
                        $order->save();
                    }
                }
            }

            ScheduleService::dispatchInform();
        })->everyThreeMinutes();

        $schedule->call(function () {
            $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
            $cacheRaces = CacheRace::where([
                ['date', '<', date('Y-m-d')]
            ])->delete();
            Log::info('Deleted successful!');

            for($i = 0; $i < count($xml->url); $i++){
                $xml->url[$i]->lastmod = date('Y-m-d');
            }


            File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
            FtpLoadingService::put();
            
            date_default_timezone_set('Europe/Moscow');
            $sitemap = simplexml_load_string(Storage::disk('sftp')->get('/var/www/rosvokzaly/data/public/sitemap.xml'));
            
            $sitemap[0]->sitemap->lastmod = date('c');
            Storage::disk('sftp')->put('/var/www/rosvokzaly/data/public/sitemap.xml', $sitemap->asXML());

            $arrivalPoints = CacheArrivalPoint::where([['created_at', '>', date('Y-m-d', strtotime('-1 day'))]])->get();
            foreach($arrivalPoints as $arrivalPoint){
                $arrivalPoint->kladr_id = KladrService::connectPointIntoKladr($arrivalPoint);
                $arrivalPoint->save();
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