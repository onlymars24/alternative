<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use App\Models\CacheArrivalPoint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertReturnSlugsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = Order::all();
        foreach($orders as $order){
            if($order->dispatchPointId && $order->arrivalPointId){
                $dispatchPoint = DispatchPoint::find($order->dispatchPointId);
                $arrivalPoint = CacheArrivalPoint::where([['arrival_point_id', '=', $order->arrivalPointId], ['dispatch_point_id', '=', $order->dispatchPointId]])->first();
        
                if(isset($dispatchPoint->kladr->arrivalPoints) && count($dispatchPoint->kladr->arrivalPoints) != 0 && isset($arrivalPoint->kladr->dispatchPoints) && count($arrivalPoint->kladr->dispatchPoints) != 0){
                    $order->dispatchReturnSlug = $arrivalPoint->kladr->slug;
                    $order->arrivalReturnSlug = $dispatchPoint->kladr->slug;
                    $order->save();
                }
            }
        }
    }
}