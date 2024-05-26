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
use Nette\Utils\DateTime;
use App\Models\Transaction;
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

Route::get('/spread/', function (Request $request) {
  dd();

  // dd($number);


  // $smsAll = Sms::where([['status', '!=', 1], ['status', '!=', 2], ['status', '!=', 6]])->get();
  $smsAll = Sms::orderByDesc('id')->first();
  dd($smsAll);
  // $sms = $smsAll[0];
  // if($sms->balance < 300){
  //   Mail::to(env('MAIL_FEEDBACK'))->send(new CustomMail('ПОПОЛНИТЕ БАЛАНС SMSAERO!!!', 'ПОПОЛНИТЕ БАЛАНС SMSAERO!!!'));
  // }
  
  // dd($sms->balance < 300);

//   $body = [
//     'body' => 'Код на росвокзалы.рф: 211211
// Поддержка в ВК-группе: vk.com/rosvokzaly',
//     'recipient' => '+7 (900) 254 8038'
//   ];
//   $body = json_encode($body);
  $whatsAppSms = Http::withHeaders([
    'Authorization' => 'c3d983e5325f664630602f4bef44234542f77d7a',
  ])
  // ->withBody($body, 'application/json')
  ->post('https://biz.wapico.ru/api/task_add.php?access_token=b58a6607f1db1b075d48afc443277fc1&number=79002548038&type=text&message=test message 123456&instance_id=664DD8720625A&timeout=0');
  $whatsAppSms = json_decode($whatsAppSms);


  $whatsAppSms2 = Http::withHeaders([
      'Authorization' => 'c3d983e5325f664630602f4bef44234542f77d7a',
  ])->get('https://biz.wapico.ru/api/task_status.php?access_token=b58a6607f1db1b075d48afc443277fc1&task_id='.$whatsAppSms->data->task_id);
  $whatsAppSms2 = json_decode($whatsAppSms2);


  dd($whatsAppSms2);

  // $regions = Http::withHeaders([
  //   'Authorization' => env('AVTO_SERVICE_KEY'),
  // ])->get(env('AVTO_SERVICE_URL').'/regions/643')->object();
  // $points = [];
  // foreach($regions as $region){
  //     $pointsTemp = Http::withHeaders([
  //         'Authorization' => env('AVTO_SERVICE_KEY'),
  //     ])->get(env('AVTO_SERVICE_URL').'/dispatch_points/'.$region->id)->object();
  //     if($pointsTemp){
  //         foreach($pointsTemp as $point){
  //             // DispatchPoint::create([
  //             //     'id' => $point->id,
  //             //     'name' => $point->name,
  //             //     'region' => $point->region,
  //             //     'details' => $point->details,
  //             //     'address' => $point->address,
  //             //     'latitude' => $point->latitude,
  //             //     'longitude' => $point->longitude,
  //             //     'okato' => $point->okato,
  //             //     'place' => $point->place
  //             // ]);
  //             $points[] = $point;
  //         }   
  //     }
  // }
  // dd($points); 
});



Route::get('/download/sitemap', function (Request $request) {
  $path = env('XML_FILE_NAME');
  if (file_exists($path)) {
      return response()->download($path);
  } else {
      return response()->json(['error' => 'File not found'], 404);
  }
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
