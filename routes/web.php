<?php

use App\Models\Sms;
use App\Models\User;
use App\Models\Bonus;
use App\Models\Kladr;
use App\Models\Order;
use App\Models\Ticket;
use XBase\TableReader;
use App\Mail\OrderMail;
use App\Models\Setting;
use App\Models\Station;
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
use App\Services\PointService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\KladrStationPage;
use App\Services\FixUserService;
use App\Services\GraphicService;
use App\Services\SitemapService;
use App\Models\CacheArrivalPoint;
use App\Services\ScheduleService;
use App\Services\BusStationService;
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
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UsersExportController;
use App\Services\PagesOnMainService;

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
  $orders = Order::where([['created_at', '>=', '2024-09-21 00:00:00'], ['created_at', '<=', '2024-09-24 18:19:00']])->get();
  // dd($orders);
  foreach($orders as $order){
    $order_obj = json_decode($order->order_info);
    if(!isset($order_obj->status) || $order_obj->status == 'B'){
      continue;
    }
    $transaction = Transaction::create([
      'StatusCode' => 0,
      'type' => 'Income',
      'order_id' => $order->id
  ]);
  $body = FermaEnum::$body;
  $item = FermaEnum::$item;
  $percent = FermaEnum::$percent;
  // $bonuses = FermaEnum::$bonuses;
  $body['Request']['Type'] = 'Income';
  $body['Request']['InvoiceId'] = $transaction->id;
  
  foreach($order->tickets as $ticket){
      $item['Label'] = 'Бил'.(!empty($ticket->ticketNum) ? ' №' : '').$ticket->ticketNum.' '.$ticket->dispatchDate.' Мст№'.$ticket->seat.' '.$ticket->lastName.' '.mb_substr($ticket->firstName, 0, 1).'. '.mb_substr($ticket->middleName, 0, 1).'.';
      $item['Price'] = $item['Amount'] = $ticket->price - $ticket->bonusesPrice;
      if($ticket->bonusesPrice > 0){
          $item['AdditionalRequisite'] = 'Цена без скидки: '.$ticket->price.'.00';
      }
      $body['Request']['CustomerReceipt']['Items'][] = $item;
  }

  
  $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total - $order->bonusesPrice + $order->duePrice;
  $user = $order->user;
  if($user->email){
      $body['Request']['CustomerReceipt']['Email'] = $user->email;
  }


  //insurance check
  if($order->insured){
      $policiesTotalRate = 0;
      foreach($order->tickets as $ticket){
          if($ticket->ticketType != 'Багажный'){
              $policy = json_decode($ticket->insurance);
              $policiesTotalRate += $policy->rate[0]->value;
          }
      }
      $insuranceReceivePosition = FermaEnum::$insurance;
      $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policiesTotalRate;
      $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
      $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total - $order->bonusesPrice + $order->duePrice + $policiesTotalRate;
  }
  

  $percent['Price'] = $percent['Amount'] = $order->duePrice;
  // $bonuses['Price'] = $bonuses['Amount'] = 0;
  // $bonuses['Label'] = 'Скидка '.$order->bonusesPrice.' бонусов';
  // $body['Request']['CustomerReceipt']['Items'][] = $bonuses;
  $body['Request']['CustomerReceipt']['Items'][] = $percent;
  // if($ticketFromDB->bonusesPrice > 0){
  //     $body['CustomUserProperty']['Name'] = 'Скидка';
  //     $body['CustomUserProperty']['Value'] = $order->bonusesPrice;
  // }


  Log::info('Body: '.json_encode($body));
  $ReceiptId = FermaService::receipt($body);
  Log::info('Receipt: '.$ReceiptId);
  $ReceiptId = json_decode($ReceiptId);

  if(isset($ReceiptId->Data->ReceiptId)){
      $ReceiptId = $ReceiptId->Data->ReceiptId;
      $receipt = FermaService::getStatus($ReceiptId);
      Log::info('Receipt: '.$receipt);
      $receipt = json_decode($receipt);
      if(isset($receipt->Data->StatusCode) && isset($receipt->Data->ReceiptId)){
          $transaction->StatusCode = $receipt->Data->StatusCode;
          $transaction->ReceiptId = $receipt->Data->ReceiptId;                
      }
      if(isset($receipt->Data->Device->OfdReceiptUrl) && !empty($receipt->Data->Device->OfdReceiptUrl)){
          $transaction->OfdReceiptUrl = $receipt->Data->Device->OfdReceiptUrl;
      }
  }


  $transaction->save();
    
    foreach($order->tickets as $ticket){
            if($ticket->status != 'R'){
              continue;
            }

            //пробитие чека
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
    
            $body['Request']['CustomerReceipt']['Items'][] = (array)json_decode($ticket->customerItem);
            $body['Request']['CustomerReceipt']['Items'][0]['Price'] = $body['Request']['CustomerReceipt']['Items'][0]['Amount'] = $ticket->repayment - $ticket->bonusesPrice;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = ($ticket->repayment - $ticket->bonusesPrice);
            // if($race->race->status->name == 'Отменён' || $race->race->status->name == 'Закрыт'){
            //     $body['Request']['CustomerReceipt']['Items'][0]['Price'] = $body['Request']['CustomerReceipt']['Items'][0]['Amount'] = $ticketFromDB->repayment;
            //     $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $ticketFromDB->repayment;
            // }
    
            
    
    
            if($ticket->insurance){
                $policy = json_decode($ticket->insurance);

                $insuranceReceivePosition = FermaEnum::$insurance;
                $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policy->rate[0]->value;
                $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
                $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] += $policy->rate[0]->value;
            }
    
            $user = $order->user;
            if($user->email){
                $body['Request']['CustomerReceipt']['Email'] = $user->email;
            }
            Log::info('Body: '.json_encode($body));
            $ReceiptId = FermaService::receipt($body);
            Log::info('Receipt: '.$ReceiptId);
            $ReceiptId = json_decode($ReceiptId);
    
            if(isset($ReceiptId->Data->ReceiptId)){
                $ReceiptId = $ReceiptId->Data->ReceiptId;
                $receipt = FermaService::getStatus($ReceiptId);
                Log::info('Receipt: '.$receipt);
                $receipt = json_decode($receipt);
                if(isset($receipt->Data->StatusCode) && isset($receipt->Data->ReceiptId)){
                    $transaction->StatusCode = $receipt->Data->StatusCode;
                    $transaction->ReceiptId = $receipt->Data->ReceiptId;                
                }
                if(isset($receipt->Data->Device->OfdReceiptUrl) && !empty($receipt->Data->Device->OfdReceiptUrl)){
                    $transaction->OfdReceiptUrl = $receipt->Data->Device->OfdReceiptUrl;
                }
            }
            $transaction->save();
    }
  }
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
