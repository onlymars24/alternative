<?php

use App\Models\Sms;
use App\Models\User;
use App\Models\Bonus;
use App\Models\Order;
use App\Models\Ticket;
use App\Mail\OrderMail;
use App\Models\Setting;
use App\Enums\FermaEnum;
use App\Mail\ReturnMail;
use App\Models\CacheRace;
use Nette\Utils\DateTime;
use App\Models\BusStation;
use App\Models\Transaction;
use App\Models\WhatsAppSms;
use App\Enums\InsuranceEnum;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Exports\WrongsExport;
use App\Mail\LeaveReviewMail;
use App\Models\DispatchPoint;
use App\Exports\ReportsExport;
use App\Services\FermaService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\FixUserService;
use App\Models\CacheArrivalPoint;
use App\Services\ScheduleService;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Services\DeletePassportService;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UsersExportController;

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



Route::get('/spread', function (Request $request) {
  FtpLoadingService::put();
  dd('');


  $busStationsMain = Setting::where('name', 'busStationsMain')->first();


  $dispatchPoints = DispatchPoint::all();
  // $dispatchPoint = DispatchPoint::find(73707);
  // dd(!$dispatchPoint->bus_stations->count());
  $busStationsSetting = [];
  foreach($dispatchPoints as $dispatchPoint){
    if(!$dispatchPoint->bus_stations->count()){
      $busStation = BusStation::create([
          'title' => $dispatchPoint->name,
          'name' => 'Автовокзал '.$dispatchPoint->name,
          'dispatch_point_id' => $dispatchPoint->id,
          'hidden' => false,
      ]);

      $newLoc = env('FRONTEND_URL').'/автовокзал/'.$busStation->title;
      $xml = simplexml_load_file(env('XML_FILE_NAME'));
      for($i = 0; $i < count($xml->url); $i++){
          $xml->url[$i]->lastmod = date('Y-m-d');
      }
      $xmlExist = false;
      // for($i = 0; $i < count($xml->url); $i++){
      //     if($xml->url[$i]->loc == $newLoc){
      //         $xmlExist = true;
      //         break;
      //     }
      // }
      if(!$xmlExist){
        $newNode = $xml->addChild('url');
        $newNode->addChild('loc', $newLoc);
        $newNode->addChild('lastmod', date('Y-m-d'));
        $newNode->addChild('changefreq', 'weekly');
        $newNode->addChild('priority', '1.0');
        File::put(env('XML_FILE_NAME'), $xml->asXML());
      }
    }
    $busStationsSetting[$dispatchPoint['region']][] = $dispatchPoint->toArray();
  }
  FtpLoadingService::put();


  foreach($busStationsSetting as $key => $region){
    usort($region, function($a, $b) {
      return strcmp($a['name'], $b['name']);
    });
    $busStationsSetting[$key] = $region;
  }
  ksort($busStationsSetting);

  $busStationsMain = Setting::where('name', 'busStationsMain')->first();
  $busStationsMain->data = json_encode(json_decode(json_encode($busStationsSetting)));
  $busStationsMain->save();
  dd($busStationsSetting);
  


});




Route::get('/download/sitemap', function (Request $request) {
  $path = env('XML_FILE_NAME');
  if (file_exists($path)) {
      return response()->download($path);
  } else {
      return response()->json(['error' => 'File not found'], 404);
  }
});

Route::get('/ticket/download/{file}/{name}', function (Request $request, $file, $name) {
  // dd($file);
  $path = Storage::disk('local')->path('/tickets/'.$file);
  // dd($path, basename($path));
  return response()->download($path, $name.'.pdf');
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
