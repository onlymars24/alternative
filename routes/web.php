<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Setting;
use App\Enums\FermaEnum;
use Nette\Utils\DateTime;
use App\Enums\InsuranceEnum;
use Illuminate\Http\Request;
use App\Exports\WrongsExport;
use App\Exports\ReportsExport;
use App\Services\FermaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/wrong/orders', function (Request $request) {
            //   Setting::create([
            //       'name' => 'dues',
            //       'data' => json_encode(['clusterDue' => 5])
            //   ]);
    //   $regions = Http::withHeaders([
//       'Authorization' => env('AVTO_SERVICE_KEY'),
//   ])->get(env('AVTO_SERVICE_URL').'/regions/643')->object();
//   $points = [];
//   foreach($regions as $region){
//       $pointsTemp = Http::withHeaders([
//           'Authorization' => env('AVTO_SERVICE_KEY'),
//       ])->get(env('AVTO_SERVICE_URL').'/dispatch_points/'.$region->id)->object();
//       if($pointsTemp){
//           foreach($pointsTemp as $point){
              // DispatchPoint::create([
              //     'id' => $point->id,
              //     'name' => $point->name,
              //     'region' => $point->region,
              //     'details' => $point->details,
              //     'address' => $point->address,
              //     'latitude' => $point->latitude,
              //     'longitude' => $point->longitude,
              //     'okato' => $point->okato,
              //     'place' => $point->place
              // ]);
//               $points[] = $point;
//           }   
//       }
//   }
//   dd($points);
  // dd($points); 
  // dd(date('Y-m-d\TH:i', strtotime('1992-07-23 00:00:00')));
    // $body = '{
    //     "product": {
    //       "code": "ON_ANTICOVID_BUS_2"
    //     },
    //     "customer_email": "customer@email.com",
    //     "customer_phone": "79201111222",
    //     "insureds": [
    //       {
    //         "first_name": "Владимир4",
    //         "last_name": "Мельников4",
    //         "patronymic": "Александрович2",
    //         "birth_date": "2000-06-10",
    //         "gender": "MALE",
    //         "phone": {
    //           "number": "79201111333"
    //         },
    //         "ticket": {
    //           "price": {
    //             "value": 900.00,
    //             "currency": "RUB"
    //           },
    //           "issue_date": "2023-06-10",
    //           "number": "5723574320584"
    //         }
    //       }
    //     ],
    //     "segments": [
    //       {
    //         "departure": {
    //           "date": "2023-12-22T08:00:01",
    //           "point": "Казань"
    //         },
    //         "arrival": {
    //           "date": "2024-01-12T12:00:00",
    //           "point": "Тольятти"
    //         }
    //       }
    //     ]
    //   }';
    
    // $response = Http::withHeaders([
    //     'X-API-Key' => env('ALFASTRAH_SERVICE_KEY'),
    // ])->withBody($body, 'application/json')->post(env('ALFASTRAH_SERVICE_URL').'/policies');

    // $response = json_decode($response);
    // // $response = $response->policies;
    // dd($response);
    // dd($response[0]->rate[0]->value);
    // return response(['response' => json_decode($response)]);

    // $response = Http::withHeaders([
    //     'X-API-Key' => env('ALFASTRAH_SERVICE_KEY'),
    // ])->withBody($body, 'application/json')->put(env('ALFASTRAH_SERVICE_URL').'/policies/'.(28939787).'/confirm');
    // $response = json_decode($response);
    // $response = $response->policies;
    // dd($response);

    // dd(InsuranceEnum::$body, InsuranceEnum::$insured);
    // dd();
    // $data = ['qw' => 123];
    // // share data to view
    // // view()->share('employee',$data);
    // $pdf = PDF::loadView('pdf_file', $data);
    // // download PDF file with download method
    // return $pdf->download('pdf_file.pdf');

    $orders = Order::all();
    $data = [];
    foreach($orders as $order){
      $order_info = json_decode($order->order_info);
      if($order_info->status == 'B'){
        $order_remoted = Http::withHeaders([
          'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/order/'.$order->id);
        $order_remoted = json_decode($order_remoted);
        // dd($order_remoted, $order_info);
        if(isset($order_remoted->status) && $order_remoted->status != $order_info->status){
          // foreach($order_info->tickets as $ticket){
          //   $tickets[] = Ticket::find($ticket->id);
          // }
          $orderFromDB = Order::find($order->id);
          
          //
          $orderFromDB->order_info = json_encode($order_remoted);
          $orderFromDB->save();
          //
          
          // $data[] = $orderFromDB;
        }
      }
    }
    dd('Ok');
    // return Excel::download(new WrongsExport($tickets), 'reports.xlsx');
});


Route::get('/kassa/callback', function (Request $request) {
    dd('sdafsd');
});

Route::get('/export/excel/', [ExcelController::class, 'export'])->name('export.excel');

Route::get('/export/pdf/', [PdfController::class, 'export'])->name('export.pdf');


Route::get('/order/confirm/', [OrderController::class, 'confirm'])->name('order.confirm');

// Route::get('/payment/callback/', [PaymentController::class, 'callback'])->name('payment.callback');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
