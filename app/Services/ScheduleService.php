<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\FermaEnum;
use App\Models\WhatsAppSms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ScheduleService
{
    public static function dispatchInform(){
        $now = date('Y-m-d H:i:s');
        $dispatchTimeLeft = date_create($now);
        date_modify($dispatchTimeLeft, '+60 min');
        $dispatchTimeLeft = date_format($dispatchTimeLeft, 'Y-m-d H:i:s');

        $dispatchTimeRight = date_create($now);
        date_modify($dispatchTimeRight, '+90 min');
        $dispatchTimeRight = date_format($dispatchTimeRight, 'Y-m-d H:i:s');
        $orders = Order::where([['moscowDispatchTime', '<', $dispatchTimeRight],
        ['moscowDispatchTime', '>', $dispatchTimeLeft], 
        ['dispatchInformed', '=', false]])->get();
        // Log::info('Заказов: '.count($orders));
        // dd($orders);
        foreach($orders as $order){
            $order_info = json_decode($order->order_info);
            $user = $order->user;
            $threeMinBefore = date_create($now);
            date_modify($threeMinBefore, '-3 min');
            $threeMinBefore = date_format($threeMinBefore, 'Y-m-d H:i:s');
            if($order_info->status != 'R' && $order_info->status != 'B' && !WhatsAppSms::where([['created_at', '<', $threeMinBefore], ['phone', '=', $user->phone]])->first()){
                
                $phoneWithoutMask = SmsService::removeMask($user->phone);
                $checkWhatsApp = Http::
                post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
                $checkWhatsApp = json_decode($checkWhatsApp);
        
                if(isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
                    $message = 
'🚍 *Скоро ваша поездка '.$order->tickets[0]->raceName.' '.date("d.m.Y", strtotime($order->tickets[0]->dispatchDate)).' в '.date("H:i", strtotime($order->tickets[0]->dispatchDate)).'*
*Приятной поездки!*

Если вам понравилось, что мы напомнили о поездке, поставьте 👍
Нам будет очень приятно 😊

🎫🚍 А если интересно, как получить кэшбэк за билеты и *компенсировать до 50% стоимости поездки*, напишите в ответ слово *"кэшбэк"*. 💳
Мы вышлем, что нужно для этого сделать.

Росвокзалы.рф';
                    $whatsAppService = Http::
                    post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$message
                    .'&instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
                    $whatsAppService = json_decode($whatsAppService);
                    Log::info('whatsAppService: '.json_encode($whatsAppService));
                    if(isset($whatsAppService->data->task_id)){
                        $whatsAppSms = WhatsAppSms::create([
                            'id' => $whatsAppService->data->task_id,
                            'phone' => $user->phone,
                            'type' => 'Сообщение о поездке',
                            'status' => 0,
                            'message' => $message
                        ]);            
                        $order->dispatchInformed = true;
                        $order->save();
                    }
                }
            }
        }
    }
}