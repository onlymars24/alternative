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
use App\Services\GraphicService;
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
  dd(CacheArrivalPoint::where('region', 'like', '%район%')->get());
  // 0201200004200
  dd(Kladr::
  where([
    // ['region', '=', 'Кемеровская обл'],
    ['name', '=', 'Междуреченск'],
    // ['district', '=', 'Павловский р-н'],
    ])
  // whereRaw("INSTR('Арбузовка', name) > 0 AND INSTR('Алтайский край', region) > 0 AND INSTR('Павловский р-н', district) > 0")
  // ->take(10)
  ->get());

  dd(Kladr::where([
    ['name', 'like', 'Белояровка'],
    // ['region', '=', 'Алтайский край'],
    ['district', '=', 'Топчихинский р-н']
  ])->get());

  dd(Kladr::where('code', 'like', '%00')->get());
  $regions = Kladr::where('code', 'like', '%00000000000')->get();
  $regionsArr = [];
  foreach($regions as $region){
    $regionsArr[] =[
      'name' => $region->name, 
      'code' => $region->code, 
      'gninmb' => $region->gninmb, 
      'socr' => $region->socr,
    ];
  }
  dd($regionsArr);
  // dd(Kladr::has('dispatchPoints')->with('dispatchPoints')->get());
  $newArr = [];
  $table = new TableReader(
    'KLADR.DBF',
    [
        'encoding' => 'cp866'
    ]
  );
  $i = 0;
  while ($record = $table->nextRecord()) {
    $i++;
    if($record->get('name') == 'Краснодарский'){
      // if($record->get('name') == 'Краснодарский' || $record->get('name') == 'Краснодар'){
      // while($record = $table->nextRecord() && $record->get('socr') != 'обл'){
        $newArr[] = [
          'name' => $record->get('name'), 
          'code' => $record->get('code'), 
          'gninmb' => $record->get('gninmb'), 
          'socr' => $record->get('socr'),
          'i' => $i
        ];
      // }

    }
    
    //or
    // echo $record->my_column;
  }
  dd($newArr);
  dd(File::get('KLADR.DBF'));
  
  
  
  GraphicService::generateImage('Томск', 'Барнаул');
  dd('');
  $imagePath = 'img/bus_bgc.jpg';
  $image = imagecreatefromjpeg($imagePath);

  $text_color = imagecolorallocate($image, 255, 255, 255); 
  
  // Function to create image which contains string. 

  $width = imagesx($image);
  $height = imagesy($image);
// Get center coordinates of image
  $centerX = $width / 2;
  $centerY = $height / 2;
// Get size of text
  $text = 'Новосибирск — \n Шерегеш';
  $font = 'fonts/FiraSansCondensed-Medium.ttf';
  $size = 70;

  list($left, $bottom, $right, , , $top) = imageftbbox($size, 0, $font, $text);
// Determine offset of text
  $left_offset = ($right - $left) / 2;
  $top_offset = ($bottom - $top) / 2;
// Generate coordinates
  $x = $centerX - $left_offset;
  $y = 400;



  imagefttext(
    $image,
    $size,
    0,
    $x,
    $y,
    $text_color,
    $font,
    $text,
    []
  );


  imagejpeg($image, 'routes/new_bus_bgc.jpg');
  dd($image);

  // $manager = new ImageManager(new Intervention\Image\Drivers\Gd\Driver());

  // reading jpeg image
  // $image = $manager->read('images/example.jpg');

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
