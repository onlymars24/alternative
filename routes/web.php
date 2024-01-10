<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Setting;
use App\Enums\FermaEnum;
use Nette\Utils\DateTime;
use App\Models\Transaction;
use App\Enums\InsuranceEnum;
use Illuminate\Http\Request;
use App\Exports\WrongsExport;
use App\Exports\ReportsExport;
use App\Services\FermaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Services\DeletePassportService;
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

Route::get('/delete/passport', function (Request $request) {
  // $orders = Order::all();
  // // $orders = [Order::find(2084362)];
  // foreach($orders as $order){
  //   $order = Order::find($order->id);
  //   $order->order_info = DeletePassportService::order($order->order_info);
  //   $order->save();
  // }
  dd('qwerty');
  // $order_json = Http::withHeaders([
  //   'Authorization' => env('AVTO_SERVICE_KEY'),
  // ])->get(env('AVTO_SERVICE_URL').'/order/2084364');
  // $order_json = DeletePassportService::order($order_json);
  // $order = json_decode($order_json);
  // dd($order);

  // $arr = [];
  // $arr[] = date('h:i:s');

  // // ожидание в течениe 10 секунд
  // sleep(300);

  // // завершение ожидания
  // $arr[] =  date('h:i:s');
  // dd($arr);

  // $data = [
  //     'userName' => config('services.payment.userName'),
  //     'password' => config('services.payment.password'),
  //     'orderId' => '0272ecbe-d053-7496-b05c-dadc0223c29b'
  // ];
  // $curl = curl_init(); // Инициализируем запрос
  // curl_setopt_array($curl, array(
  //     // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
  //     CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/getOrderStatus.do', 
  //     CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
  //     CURLOPT_POST => true, // Метод POST
  //     CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
  // ));
  // $orderFromBank = curl_exec($curl); // Выполняем запрос
  // curl_close($curl); // Закрываем соединение

  // $orderFromBank = json_decode($orderFromBank);
  // dd($orderFromBank);
  // Mail::raw('Текст письма', function($message) {
  //     $message->to('marsel.galimov.24@mail.ru', 'Имя Получателя');
  //     $message->subject('Тема письма');
  // });


  // $to = "marsel.galimov.24@mail.ru";
  // $subject = "Тема письма";
  // $message = "Текст письма";
  
  // // Дополнительные заголовки
  // $headers = 'From: example@example.com' . "\r\n" .
  //     'Reply-To: example@example.com' . "\r\n" .
  //     'X-Mailer: PHP/' . phpversion();
  
  // mail($to, $subject, $message, $headers);


  // $to = 'marsel.galimov.24@mail.ru';
  // $subject = 'Тема письма';
  // $data = ['message' => 'Текст сообщения'];

  // Mail::send([], [], function ($message) use ($to, $subject, $data) {
  //     $message->to($to)
  //             ->subject($subject)
  //             ->setBody($data['message']);
  // });


// Mail::raw('Текст сообщения', function($message) {
//   $message->to('marsel.galimov.24@mail.ru', 'Имя получателя')->subject('Тема сообщения');
// });

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

    // $orders = Order::all();
    // $data = [];
    // foreach($orders as $order){
    //   $order_info = json_decode($order->order_info);
    //   if($order_info->status == 'B'){
    //     $order_remoted = Http::withHeaders([
    //       'Authorization' => env('AVTO_SERVICE_KEY'),
    //     ])->get(env('AVTO_SERVICE_URL').'/order/'.$order->id);
    //     $order_remoted = json_decode($order_remoted);
    //     // dd($order_remoted, $order_info);
    //     if(isset($order_remoted->status) && $order_remoted->status != $order_info->status){
    //       // foreach($order_info->tickets as $ticket){
    //       //   $tickets[] = Ticket::find($ticket->id);
    //       // }
    //       $orderFromDB = Order::find($order->id);
          
    //       //
    //       //
          
    //       // $data[] = $orderFromDB;
    //     }
    //   }
    // }
    // dd('Ok');
    // return Excel::download(new WrongsExport($tickets), 'reports.xlsx');
});


Route::get('/kassa/callback', function (Request $request) {
    dd('sdafsd');
});

Route::get('/export/excel/', [ExcelController::class, 'export'])->name('export.excel');

Route::get('/export/pdf/', [PdfController::class, 'export'])->name('export.pdf');


Route::get('/order/confirm/', [OrderController::class, 'confirm'])->name('order.confirm');

Route::get('/add/return/hold/{ticketId}', function (Request $request) {
  dd('');

  $ticketId = $request->ticketId;
  // $holdTicket = 3150;
  $ticket = Ticket::find($ticketId);
  if($ticket->price == $ticket->repayment){
    dd('Уже было');
  }
  $order = $ticket->order;
  
  $transaction = Transaction::create([
    'StatusCode' => 0,
    'type' => 'IncomeReturn',
    'order_id' => $order->id
  ]);
  $body = FermaEnum::$body;
  $item = FermaEnum::$item;
  $percent = FermaEnum::$percent;
  $body['Request']['Type'] = 'IncomeReturn';
  $body['Request']['InvoiceId'] = $transaction->id;


  $hold = $ticket->price - $ticket->repayment;

  //возврат удержания
  $orderBundle = (array)json_decode($ticket->orderBundle);
  $data = [
    'userName' => config('services.payment.userName'),
    'password' => config('services.payment.password'),
    'orderId' => $order->bankOrderId,
    'amount' => $hold * 100,
    'positionId' => $orderBundle['positionId']
  ];
  $curl = curl_init(); // Инициализируем запрос
  curl_setopt_array($curl, array(
      // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
      CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do', 
      CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
      CURLOPT_POST => true, // Метод POST
      CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
  ));
  $repayment = curl_exec($curl);

  $data = [
    'userName' => config('services.payment.userName'),
    'password' => config('services.payment.password'),
    'orderId' => $order->bankOrderId,
    'amount' => $ticket->duePrice * 100,
    'positionId' => $order->tickets->count()+2
  ];
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do',
    CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
    CURLOPT_POST => true, // Метод POST
    CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
  ));
  $repayment = curl_exec($curl); // Выполняем запрос
  //возврат комиссии сайта


  $body['Request']['CustomerReceipt']['Items'][] = (array)json_decode($ticket->customerItem);
  $body['Request']['CustomerReceipt']['Items'][0]['Price'] = $body['Request']['CustomerReceipt']['Items'][0]['Amount'] = $hold;
  $percent = FermaEnum::$percent;
  $percent['Price'] = $percent['Amount'] = $ticket->duePrice;
  $body['Request']['CustomerReceipt']['Items'][] = $percent;
  $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum']  = $hold + $ticket->duePrice;

  $ReceiptId = FermaService::receipt($body);
  Log::info('Receipt: '.$ReceiptId);
  $ReceiptId = json_decode($ReceiptId);
  $ReceiptId = $ReceiptId->Data->ReceiptId;
  $receipt = FermaService::getStatus($ReceiptId);
  Log::info('Receipt: '.$receipt);
  $receipt = json_decode($receipt);

  $transaction->StatusCode = $receipt->Data->StatusCode;
  $transaction->ReceiptId = $receipt->Data->ReceiptId;
  if(isset($receipt->Data->Device->OfdReceiptUrl) && !empty($receipt->Data->Device->OfdReceiptUrl)){
      $transaction->OfdReceiptUrl = $receipt->Data->Device->OfdReceiptUrl;
  }
  $transaction->save();
  $ticket->repayment = $ticket->price;
  $ticket->save();
  dd('ok');
});


Route::get('/return/receive', function (Request $request) {

});

// Route::get('/payment/callback/', [PaymentController::class, 'callback'])->name('payment.callback');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
