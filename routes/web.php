<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Mail\OrderMail;
use App\Models\Setting;
use App\Enums\FermaEnum;
use App\Mail\ReturnMail;
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

Route::get('/fix/confirm', function (Request $request) {
  dd('fix');


  $orderId = null;
$order_json = Http::withHeaders([
  'Authorization' => env('AVTO_SERVICE_KEY'),
])->get(env('AVTO_SERVICE_URL').'/order/'.$orderId);

Log::info('obj_json: '.$order_json);
$order_obj = json_decode($order_json);
if(!isset($order_obj->id)){
    return;
}
$order_json = DeletePassportService::order($order_json);
$order_obj = json_decode($order_json);

$transaction = Transaction::create([
    'StatusCode' => 0,
    'type' => 'Income',
    'order_id' => $orderId
]);
$body = FermaEnum::$body;
$item = FermaEnum::$item;
$percent = FermaEnum::$percent;
$body['Request']['Type'] = 'Income';
$body['Request']['InvoiceId'] = $transaction->id;
foreach($order_obj->tickets as $ticket){
    $ticketFromDB = Ticket::find($ticket->id);
    $ticketFromDB->update((array)$ticket);
    $url = env('AVTO_SERVICE_TICKET_URL').'/'.$ticket->hash.'.pdf';
    $file_name = basename($url);
    file_put_contents('tickets/'.$file_name, file_get_contents($url));

    $item['Label'] = 'Бил'.(!empty($ticket->ticketNum) ? ' №' : '').$ticket->ticketNum.' '.$ticket->dispatchDate.' Мст№'.$ticket->seat.' '.$ticket->lastName.' '.mb_substr($ticket->firstName, 0, 1).'. '.mb_substr($ticket->middleName, 0, 1).'.';
    $item['Price'] = $ticket->price;
    $item['Amount'] = $ticket->price;
    $body['Request']['CustomerReceipt']['Items'][] = $item;
    $ticketFromDB->customerItem = json_encode($item);
    $ticketFromDB->save();
}

$order = Order::find($orderId);
$order->order_info = $order_json;


$body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total + $order->duePrice;
$user = $order->user;
if($user->email){
    $body['Request']['CustomerReceipt']['Email'] = $user->email;
}


//insurance check
if($order->insured){
    $policiesTotalRate = 0;
    foreach($order_obj->tickets as $ticket){
        $ticketFromDB = Ticket::find($ticket->id);
        $insuranceBody = InsuranceEnum::$body;
        $insuranceBody['segments'][0]['departure']['date'] = date('Y-m-d\TH:i', strtotime($ticketFromDB->dispatchDate));
        $insuranceBody['segments'][0]['departure']['point'] = $ticketFromDB->dispatchStation;
        $insuranceBody['segments'][0]['arrival']['date'] = date('Y-m-d\TH:i', strtotime($ticketFromDB->arrivalDate));
        $insuranceBody['segments'][0]['arrival']['point'] = $ticketFromDB->arrivalStation;
        // Log::info('arrivalStation: '.$ticketFromDB->arrivalStation);

        $insuranceBodyInsured = InsuranceEnum::$insured;
        $insuranceBodyInsured['first_name'] = $ticketFromDB->firstName;
        $insuranceBodyInsured['last_name'] = $ticketFromDB->lastName;
        $insuranceBodyInsured['patronymic'] = $ticketFromDB->middleName;
        $insuranceBodyInsured['birth_date'] = date('Y-m-d', strtotime($ticketFromDB->birthday));
        $insuranceBodyInsured['gender'] = $ticketFromDB->gender == 'M' ? 'MALE' : 'FEMALE';
        $insuranceBodyInsured['phone']['number'] = $user->phone;
        $insuranceBodyInsured['ticket']['price']['value'] = $ticketFromDB->price;
        $insuranceBodyInsured['ticket']['issue_date'] = $ticketFromDB->created_at;
        $insuranceBodyInsured['ticket']['number'] = $ticketFromDB->ticketNum;

        $insuranceBody['insureds'][] = $insuranceBodyInsured;

        $alfaStrahResponse = Http::withHeaders([
            'X-API-Key' => env('ALFASTRAH_SERVICE_KEY'),
        ])->withBody(json_encode($insuranceBody), 'application/json')->post(env('ALFASTRAH_SERVICE_URL').'/policies?confirm=true');
        Log::info($alfaStrahResponse);
        $alfaStrahResponse = json_decode($alfaStrahResponse);
        $policies = $alfaStrahResponse->policies;
        $policy = $policies[0];

        
        $policiesTotalRate += $policy->rate[0]->value;
        $ticketFromDB->insurance = json_encode($policy);
        $ticketFromDB->save();
    }
    $insuranceReceivePosition = FermaEnum::$insurance;
    $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policiesTotalRate;
    $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
    $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total + $order->duePrice + $policiesTotalRate;
}


$percent['Price'] = $percent['Amount'] = $order->duePrice;
$body['Request']['CustomerReceipt']['Items'][] = $percent;        

Log::info('Body: '.json_encode($body));
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
$data = [
    'userName' => config('services.payment.userName'),
    'password' => config('services.payment.password'),
    'orderId' => $order->bankOrderId
];
$curl = curl_init(); // Инициализируем запрос
curl_setopt_array($curl, array(
    // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
    CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/getOrderStatus.do', 
    CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
    CURLOPT_POST => true, // Метод POST
    CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
));
$orderFromBank = curl_exec($curl); // Выполняем запрос
curl_close($curl); // Закрываем соединение

$orderFromBank = json_decode($orderFromBank);

$order->ip = isset($orderFromBank->Ip) ? $orderFromBank->Ip : null;
$order->pan = isset($orderFromBank->Pan) ? $orderFromBank->Pan : null;
$order->save();

if($order->user->email){
    Mail::to($order->user->email)->bcc(env('TICKETS_MAIL'))->send(new OrderMail($order->tickets));
}
Log::info('Order\'s confirmed'.$orderId.' '.$order->bankOrderId);
});

Route::get('/mail/test/', function (Request $request) {

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
