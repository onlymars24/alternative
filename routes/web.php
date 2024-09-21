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

  $ordersIDs = [143723259, 143723099];
  foreach($ordersIDs as $id){
    $order = Order::find($id);
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
        Log::info('sent!');
    }
    else{
        Log::info('not sent!');
    }

    // $user = User::find($order->user_id);
    // if($order->bonusesPrice > 0){
    //     $user->bonuses_balance = $user->bonuses_balance - $order->bonusesPrice;
    //     Bonus::create([
    //         'amount' => $order->bonusesPrice,
    //         'transaction' => 'minus',
    //         'user_id' => $user->id,
    //         'order_id' => $order->id,
    //         'user_phone' => $user->phone,
    //         'descr' => 'Оформлен заказ с ID: '.$order->id
    //     ]);
    // }
    
    // $user->save();

    $acqDuePercent = null;
    $setting = Setting::where('name', 'dues')->first();
    $dues = (array)json_decode($setting->data);
    Log::info(json_encode($dues));

    if($order->pan){
        $acqDuePercent = $dues['acqCardDue'];
    }
    else{
        $acqDuePercent = $dues['acqSbpDue'];
    }


    foreach($order->tickets as $ticket){
        $ticket->acqPercent = $acqDuePercent;
        $tempDuePrice = $ticket->price + $ticket->duePrice - $ticket->bonusesPrice;
        if($ticket->insurance){
            $tempInsurance = json_decode($ticket->insurance);
            if(isset($tempInsurance->rate[0]->value) && $tempInsurance->rate[0]->value){
                $tempDuePrice += $tempInsurance->rate[0]->value;
            }
        }
        $resultAcqPrice = $tempDuePrice * $acqDuePercent / 100;
        if($resultAcqPrice < 5){
            $resultAcqPrice = 5;
        }
        $ticket->acqPrice = $resultAcqPrice;
        $ticket->save();
    }
    $phoneWithoutMask = SmsService::removeMask($order->user->phone);
    $checkWhatsApp = Http::
    post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
    $checkWhatsApp = json_decode($checkWhatsApp);
  
    if(isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
        $message = '💳 *Получите Кэшбэк!*

*Благодарим за оформление электронного билета!*

Рекомендуем сразу посмотреть билеты на обратный рейс (при его наличии) на сайте
Росвокзалы.рф

🎫🚍 Также для вас доступна возможность *компенсировать до 50% стоимости поездки.* Если хотите получить частичную компенсацию, напишите в ответ слово *"кэшбэк"*.💰

Мы вышлем, что нужно для этого сделать.';
        $whatsAppService = Http::
        post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$message
        .'&instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
        $whatsAppService = json_decode($whatsAppService);
        Log::info('whatsAppService: '.json_encode($whatsAppService));
        if(isset($whatsAppService->data->task_id)){
            $whatsAppSms = WhatsAppSms::create([
                'id' => $whatsAppService->data->task_id,
                'phone' => $order->user->phone,
                'type' => 'Подтверждение заказа',
                'status' => 0,
                'message' => $message
            ]);            
        }
    }
  }
  dd('ok!');



  $pages = KladrStationPage::where([['kladr_id', '<>', null]])->get();
  foreach($pages as $page){
    $page->name = 'Автовокзалы и автостанции '.$page->kladr->name;
    $page->save();
  }

  PagesOnMainService::recreate();
  dd('');

$busStation = BusStation::find(11);
$page = KladrStationPage::find(131);
$page->content = json_decode($busStation->data)->content;
$page->save();
  dd($busStation->data, json_decode($busStation->data)->content);
  $dispatchPoints = DispatchPoint::all();
  $kladrs = Kladr::has('dispatchPoints')->get();

  foreach($kladrs as $kladr){
      if(!$kladr->kladrStationPage){
          $pageArray = ['hidden' => false];
          $pageArray['url_settlement_name'] = str_replace([' ', '/'], ['_', '-'], $kladr->name);
          $pageArray['name'] = $kladr->name.' Автовокзалы и автостанции';
          $pageArray['description'] = $kladr->name.' Автовокзалы и автостанции: расписание, справочная, билеты на автобус';
          $pageArray['kladr_id'] = $kladr->id;
          $newLocType = 'расписание';
          $pageArray['url_region_code'] = mb_strcut($kladr->code, 0, 2);
          $page = KladrStationPage::create($pageArray);
          SitemapService::add(env('FRONTEND_URL').'/'.$newLocType.'/'.$page->url_region_code.'/'.$page->url_settlement_name, 'weekly');
      }
  }

  foreach($dispatchPoints as $dispatchPoint){
      $pageArray = ['hidden' => false];
      if(!$dispatchPoint->station){
          $station = Station::create([
              'name' => $dispatchPoint->name,
              'address' => $dispatchPoint->address,
              'longitude' => $dispatchPoint->longitude,
              'latitude' => $dispatchPoint->latitude,
              'kladr_id' => $dispatchPoint->kladr_id
          ]);    
          $dispatchPoint->station_id = $station->id;
          $dispatchPoint->save();       
      }
      else{
          $station = $dispatchPoint->station;
      }
      if(!$station->kladrStationPage){
          $kladr = $station->kladr;
          if(!$kladr){
              continue;
          }
          $pageArray['url_settlement_name'] = str_replace([' ', '/'], ['_', '-'], $station->name);
          $pageArray['name'] = 'Автовокзал '.$station->name;
          $pageArray['description'] = 'Автовокзал '.$station->name.': расписание, справочная, билеты на автобус';
          $pageArray['station_id'] = $station->id;
          $pageArray['url_region_code'] = mb_strcut($kladr->code, 0, 2);
          $page = KladrStationPage::create($pageArray);
          $newLocType = 'автовокзал';
          Log::info('sitemap1');
          SitemapService::add(env('FRONTEND_URL').'/'.$newLocType.'/'.$page->url_region_code.'/'.$page->url_settlement_name, 'weekly');
  
          
      }
  }



  dd(simplexml_load_file(public_path(env('XML_FILE_NAME'))));
  dd(CacheArrivalPoint::where([['created_at', '>', date('Y-m-d', strtotime('-1 day'))]])->get());
  dd(date('Y-m-d'));
  dd(DispatchPoint::with('kladr.arrivalPoints', 'station')->get()->toArray());
  dd(Kladr::has('dispatchPoints')->orHas('arrivalPoints')->where('name', 'like', '%'.$request->kladrFilter.'%')->take(10)->get());
  dd(Kladr::has('arrivalPoints')
  ->orHas('dispatchPoints')
  ->get());


  BusStationService::createByDispatchPoint(80153);
  dd('');
  dd(BusStation::whereHas('dispatchPoint', function($query){
    $query->where('kladr_id', '=', 241805);
  })->get());

  $string = 'Емельяново а/п';

  $string = str_replace([' ', '/'], ['_', '-'], $string);

  dd($string);
  // dd(
  //   BusStation::where([['kladr_id', '<>', null], ['dispatch_point_id', '=', null]])->with('kladr.dispatchPoints')->get(),
  //   BusStation::where([['kladr_id', '=', null], ['dispatch_point_id', '<>', null]])->with('dispatchPoint.kladr')->get()
  // );
  $busStations = BusStation::all();
  foreach($busStations as $busStation){
    // $busStation->content = json_decode($busStation->data)->content;
    
    $busStation->save();
  }
  dd('ok');
  
  $busStations = BusStation::with('kladr')->get();
  $busStationsSetting = [];
  foreach($busStations as $station){
    if($station->kladr){
      $kladr = $station->kladr->toArray();
      if($kladr['region']){
        $busStationsSetting[$kladr['region']][] = $kladr;
      }
      
    }
  }
  // dd($busStationsSetting);

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


//   $dispatchPoints = PointService::dispatchKandE();

//   $busStationsSetting[$dispatchPoint['region']][] = $dispatchPoint->toArray();
// FtpLoadingService::put();


// foreach($busStationsSetting as $key => $region){
//   usort($region, function($a, $b) {
//     return strcmp($a['name'], $b['name']);
//   });
//   $busStationsSetting[$key] = $region;
// }
// ksort($busStationsSetting);





  dd($dispatchPoints);

  dd(Kladr::where([['code', 'like', '%00']])->take(10)->get());
  dd('11' <= '11');
  $arrivalPoints = CacheArrivalPoint::where([['dispatch_point_id', '=', 5107], ['name', '=', 'Краснодар']])->whereHas('kladr', function(Builder $query){
    $query->has('arrivalPoints', '>', 1);
  })
  ->with('kladr.arrivalPoints')
  // ->take(10)
  ->get();

  // $arrivalPoints1 = CacheArrivalPoint::doesntHave('kladr')->where([['dispatch_point_id', '=', 5107], ['name', '=', 'Краснодар']])->get();
  dd($arrivalPoints);


  $races = [];

  $races1 = Http::withHeaders([
    'Authorization' => env('AVTO_SERVICE_KEY'),
  ])->get(env('AVTO_SERVICE_URL').'/races/66690/150016/2024-08-07/')->object();
  $races = array_merge($races, $races1);

  dd($races);

  $races2 = Http::withHeaders([
    'Authorization' => env('AVTO_SERVICE_KEY'),
  ])->get(env('AVTO_SERVICE_URL').'/races/66690/150016/2024-08-08/')->object();
  $races = array_merge($races, $races2);


  $races3 = Http::withHeaders([
    'Authorization' => env('AVTO_SERVICE_KEY'),
  ])->get(env('AVTO_SERVICE_URL').'/races/66690/150016/2024-08-09/')->object();
  $races = array_merge($races, $races3);
  // dd(array_merge($array1, $array2));
  

  dd($races);
  // dd('');
  $arrivalPoints1 = CacheArrivalPoint::doesntHave('kladr')->where([['dispatch_point_id', '=', 66690]])->get();
  $arrivalPoints2 = CacheArrivalPoint::where([['dispatch_point_id', '=', 66690]])->whereHas('kladr', function(Builder $query){
      $query->has('arrivalPoints', '>', 1);
  })
  ->with('kladr.arrivalPoints')
  ->get();
  dd([
      'kladrs' => Kladr::has('arrivalPoints', '=', 1)->whereHas('arrivalPoints', function(Builder $query){
            $query->where('dispatch_point_id', '=', 66690);
        })->with('arrivalPoints')->get(),
      'arrivalPoints' => $arrivalPoints1->concat($arrivalPoints2)
      // 'arrivalPoints' => $arrivalPoints2
  ]);
  
  // dd(Kladr::has('dispatchPoints', '=', 1)->with('dispatchPoints')->get());
  // dd(Kladr::
  //   // has('arrivalPoints')
  //   whereHas('arrivalPoints', function (Builder $query) {
  //     $query->where('dispatch_po', '=', 5107);
  // })
  // ->with(['arrivalPoints'])
  // ->take(10)
  // ->get());
  $dispatchPoints1 = DispatchPoint::
    whereHas('kladr', function(Builder $query){
      $query->has('dispatchPoints', '=', 1);
    })
    ->with('kladr.dispatchPoints')
    ->get();  
  $dispatchPoints2 = DispatchPoint::doesntHave('kladr')->get();
  $dispatchPoints2 = $dispatchPoints1->concat($dispatchPoints2);
  dd($dispatchPoints2);
//   $dispatchPoints = DispatchPoint::all();
//   foreach($dispatchPoints as $point){
//     $dispatchPoint = DispatchPoint::create([
//         'id' => $point->id,
//         'name' => $point->name,
//         'region' => $point->region,
//         'details' => $point->details,
//         'address' => $point->address,
//         'latitude' => $point->latitude,
//         'longitude' => $point->longitude,
//         'okato' => $point->okato,
//         'place' => $point->place
//     ]);
//     $arrival_points_remoted = Http::withHeaders([
//         'Authorization' => env('AVTO_SERVICE_KEY'),
//     ])->get(env('AVTO_SERVICE_URL').'/arrival_points/'.$point->id)->object();
//     foreach($arrival_points_remoted as $point){
//         $arrival_points = CacheArrivalPoint::create([
//             'arrival_point_id' => $point->id,
//             'name' => $point->name,
//             'region' => $point->region,
//             'details' => $point->details,
//             'address' => $point->address,
//             'latitude' => $point->latitude,
//             'longitude' => $point->longitude,
//             'okato' => $point->okato ? $point->okato : '1',
//             'place' => $point->place ? $point->place : 1,
//             'dispatch_point_id' => $dispatchPoint->id,
//         ]);                
//     }
//     // $points[] = $point;
// }  
  
  // dd();
  // dd(CacheArrivalPoint::where('region', 'like', '%район%')->get());
  // 0201200004200
  dd(Kladr::
  where([
    // ['region', '=', 'Павлодарская обл'],
    ['name', '=', 'Кемерово'],
    // ['district', '=', 'Ижморский р-н'],
    ])
  // whereRaw("INSTR('Кемерово', name) > 0 AND INSTR('Кемеровская обл', region) > 0")
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
