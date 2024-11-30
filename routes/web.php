<?php

use App\MyApp;
// use PDFMerger;
use App\Models\Sms;
use App\Models\Role;
use App\Models\User;
use App\Models\Bonus;
use App\Models\Kladr;
use App\Models\Order;
use App\Mail\DumpMail;
use App\Models\Ticket;
use GuzzleHttp\Client;
use XBase\TableReader;
use App\Mail\OrderMail;
use App\Models\Setting;
use App\Models\Station;
use setasign\Fpdi\Fpdi;
use App\Enums\FermaEnum;
use App\Mail\ReturnMail;
use App\Models\CacheRace;
use App\Models\OtpMember;
use Nette\Utils\DateTime;
use App\Mail\ErrorApiMail;
use App\Models\BusStation;
use App\Mail\OtpMemberMail;
use App\Models\SitemapPage;
use App\Models\Transaction;
use App\Models\WhatsAppSms;
use App\Enums\InsuranceEnum;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Exports\WrongsExport;
use App\Mail\LeaveReviewMail;
use App\Models\DispatchPoint;
use App\Services\MailService;
use App\Services\SlugService;
use App\Exports\ReportsExport;
use App\Services\AdPdfService;
use App\Services\FermaService;
use App\Services\KladrService;
use App\Services\PointService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\KladrStationPage;
use App\Services\FixUserService;
use App\Services\GraphicService;
use App\Services\SitemapService;
use App\Models\CacheArrivalPoint;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\DB;
use App\Services\BusStationService;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\Log;
use App\Services\PagesOnMainService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Services\DeletePassportService;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToArray;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\KladrController;
use App\Http\Controllers\OrderController;
use Illuminate\Database\Eloquent\Builder;
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


// Route::get('/sitemap/reload', function (Request $request) {
//   ini_set('max_execution_time', 600);
//   $xml = simplexml_load_file(public_path('sitemap.local.xml'));

//   DB::table('sitemap_pages')->delete();


//   $pages = KladrStationPage::all();
//   foreach($pages as $page){
//     $result[] = env('FRONTEND_URL').'/'.($page->kladr_id ? 'расписание' : 'автовокзал').'/'.$page->url_region_code.'/'.$page->url_settlement_name;
//   }
//   // dd($res);
//   // $result = [];
//   $dispatchData = PointService::dispatchKandE();
//   foreach($dispatchData as $dispatchItem){
//     $arrivalData = null;
//     if(array_key_exists('details', $dispatchItem)){
//       $arrivalData = PointService::kAndE($dispatchItem['id']);
//       foreach($arrivalData as $arrivalItem){
//         $result[env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug']] = env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug'];
//       }
//     }
//     else{
//       // dd(Kladr::find($dispatchItem['id']));
//       $kladr = Kladr::find($dispatchItem['id']);
//       // if(!$kladr){
//       //   dd($kladr, $dispatchItem, $dispatchItem['id']);
//       // }
//       $dispatchPoints = $kladr->dispatchPoints;
      
//       foreach($dispatchPoints as $dispatchPoint){
//         $arrivalData = PointService::kAndE($dispatchPoint->id);
//         foreach($arrivalData as $arrivalItem){
//           $result[env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug']] = env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug'];
//         }
//       }
//     }
    
//     // foreach($arrivalData as $arrivalItem){
//     //   
//     // }
//   }
//   // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
//   foreach($result as $item){
//     $xml = SitemapService::add($item, stripos($item, 'автобус') === false ? 'weekly' : 'daily', $xml);
//   }
//   File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
//   FtpLoadingService::put();
//   // dd($xml);  
// });

Route::get('/sitemap/reload', function (Request $request) {
  dd('');
  ini_set('max_execution_time', 600);  
  $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
  $sitemaps = SitemapPage::all();
  foreach($sitemaps as $sitemap){
    $xml = SitemapService::add($sitemap->url, stripos($sitemap, 'автобус') === false ? 'weekly' : 'daily', $xml);
  }
  File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
  FtpLoadingService::put();
});

Route::get('/spread', function (Request $request) {
  $order_json = Http::withHeaders([
      'Authorization' => env('AVTO_SERVICE_KEY'),
  ])->post(env('AVTO_SERVICE_URL').'/order/145298096');
  Log::info('; obj_json: '.$order_json);
  $order_obj = json_decode($order_json);
  if(!isset($order_obj->id)){
      MailService::sendError(env('AVTO_SERVICE_URL').'/order/confirm/', $order_obj);
      return;
  }
  $order_json = DeletePassportService::order($order_json);
  $order_obj = json_decode($order_json);


  $body = FermaEnum::$body;
  $item = FermaEnum::$item;
  $percent = FermaEnum::$percent;
  // $bonuses = FermaEnum::$bonuses;
  $body['Request']['Type'] = 'Income';

  foreach($order_obj->tickets as $ticket){
      $ticketFromDB = Ticket::find($ticket->id);
      $ticketFromDB->update((array)$ticket);
      $url = env('AVTO_SERVICE_TICKET_URL').'/'.$ticket->hash.'.pdf';
      $file_name = 'tickets/'.basename($url);
      file_put_contents($file_name, file_get_contents($url));

      AdPdfService::mergePdf($file_name);

      $item['Label'] = 'Бил'.(!empty($ticket->ticketNum) ? ' №' : '').$ticket->ticketNum.' '.$ticket->dispatchDate.' Мст№'.$ticket->seat.' '.$ticket->lastName.' '.mb_substr($ticket->firstName, 0, 1).'. '.mb_substr($ticket->middleName, 0, 1).'.';
      $item['Price'] = $item['Amount'] = $ticketFromDB->price - $ticketFromDB->bonusesPrice;
      if($ticketFromDB->bonusesPrice > 0){
          $item['AdditionalRequisite'] = 'Цена без скидки: '.$ticketFromDB->price.'.00';
      }
      $body['Request']['CustomerReceipt']['Items'][] = $item;
      $ticketFromDB->customerItem = json_encode($item);
      $ticketFromDB->confirmed_at = $order_obj->finished;
      $ticketFromDB->save();
  }

  $order = Order::find($order_obj->id);
  $order->order_info = $order_json;

  
  $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total - $order->bonusesPrice + $order->duePrice;
  $user = $order->user;
  if($user->email){
      $body['Request']['CustomerReceipt']['Email'] = $user->email;
  }


  //insurance check
  if($order->insured){
      $policiesTotalRate = 0;
      foreach($order_obj->tickets as $ticket){
          if($ticket->ticketType != 'Багажный'){
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
              if(!isset($alfaStrahResponse->policies[0]->rate[0]->value)){
                  MailService::sendError(env('ALFASTRAH_SERVICE_URL').'/policies?confirm=true', $alfaStrahResponse);
              }
              else{
                  $policies = $alfaStrahResponse->policies;
                  $policy = $policies[0];
                  $policiesTotalRate += $policy->rate[0]->value;
                  $ticketFromDB->insurance = json_encode($policy);
                  $ticketFromDB->save();
              }
          }
      }
      if($policiesTotalRate){
          $insuranceReceivePosition = FermaEnum::$insurance;
          $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policiesTotalRate;
          $order->insurancePrice = $policiesTotalRate;
          $order->save();
          $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
          $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total - $order->bonusesPrice + $order->duePrice + $policiesTotalRate;                
      }

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

  FermaService::create($order_obj->id, $user, $body);
  // $transaction = Transaction::create([
  //     'StatusCode' => 0,
  //     'type' => 'Income',
  //     'order_id' => $request->orderNumber
  // ]);        

  // $body['Request']['InvoiceId'] = $transaction->id;

  // Log::info('Body: '.json_encode($body));
  // $ReceiptId = FermaService::receipt($body);
  // Log::info('Receipt: '.$ReceiptId);
  // $ReceiptId = json_decode($ReceiptId);

  // if(isset($ReceiptId->Data->ReceiptId)){
  //     $ReceiptId = $ReceiptId->Data->ReceiptId;
  //     $receipt = FermaService::getStatus($ReceiptId);
  //     Log::info('Receipt: '.$receipt);
  //     $receipt = json_decode($receipt);
  //     if(!isset($receipt->Data->StatusCode) || !isset($receipt->Data->ReceiptId) 
  //     || !isset($receipt->Data->Device->OfdReceiptUrl) || empty($receipt->Data->Device->OfdReceiptUrl)){
  //         MailService::sendError(env('FERMA_SERVICE_URL').'/kkt/cloud/receipt', $receipt);
  //     }
  //     if(isset($receipt->Data->StatusCode) && isset($receipt->Data->ReceiptId)){
  //         $transaction->StatusCode = $receipt->Data->StatusCode;
  //         $transaction->ReceiptId = $receipt->Data->ReceiptId;
  //     }
  //     if(isset($receipt->Data->Device->OfdReceiptUrl) && !empty($receipt->Data->Device->OfdReceiptUrl)){
  //         $transaction->OfdReceiptUrl = $receipt->Data->Device->OfdReceiptUrl;
  //     }
  // }
  // else{
  //     MailService::sendError(env('FERMA_SERVICE_URL').'/kkt/cloud/receipt', $ReceiptId);
  // }


  // $transaction->save();
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
  if($order->bonusesPrice > 0){
      $user->bonuses_balance = $user->bonuses_balance - $order->bonusesPrice;
      Bonus::create([
          'amount' => $order->bonusesPrice,
          'transaction' => 'minus',
          'user_id' => $user->id,
          'order_id' => $order->id,
          'user_phone' => $user->phone,
          'descr' => 'Оформлен заказ с ID: '.$order->id
      ]);
  }
  
  $user->save();

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
  $phoneWithoutMask = SmsService::removeMask($user->phone);
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
              'phone' => $user->phone,
              'type' => 'Подтверждение заказа',
              'status' => 0,
              'message' => $message
          ]);            
      }
      else{
          MailService::sendError(env('WAPICO_URL').'/task_add.php', $whatsAppService);
      }
  }
  else{
      MailService::sendError(env('WAPICO_URL').'/send.php', $checkWhatsApp);
  }




  dd('');




  
  ini_set('max_execution_time', 600);  
  $newPoints = PointService::checkNewPoints();

  if(count($newPoints) > 0){
      PointService::addNewPoints($newPoints);
      Log::info('Новые точки добавлены!');
  }
  dd($newPoints);  

  Mail::to([env('ERROR_MAIL_YOUGILE'), env('ERROR_MAIL_PAVEL'), env('ERROR_MAIL_MARSEL')])->send(new DumpMail('$test', '$test', '$test'));
  dd('');
  $sitemapPath = public_path('sitemap.local.xml');

  if (File::exists($sitemapPath)) {
      // Очистить содержимое файла
      File::put($sitemapPath, '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
</urlset>');
      // return response('Sitemap has been cleared!', 200);
      dd('');
  }

  // dd(stripos('kladrs-123124', 'kladrs'));
  $arrivalData = PointService::arrivalDataBySourceId('kladrs-241805');
  dd(array_filter($arrivalData, function($item) {
    return $item->slug == 'Псков' 
    // && stripos($item->sourceId, 'stations') !== false // Четные числа
    ;
  }));
  // Пример массива
  $array = [
    'apple' => 'fruit',
    'banana' => 'fruit',
    'appreciate' => 'verb',
    'апange' => 'fruit',
    'арбуз' => 'fruit',
    'аПельсин' => 'fruit',
    'Апрель' => 'month',
    'арена' => 'place'
  ];

  // Строка, с которой должны совпадать ключи
  $str = 'ап';

  // Приводим строку $str к нижнему регистру для сравнения
  $str_lower = mb_strtolower($str, 'UTF-8');

  // Функция для сортировки массива по ключам
  uksort($array, function($a, $b) use ($str_lower) {
    // Приводим ключи к нижнему регистру для регистронезависимого сравнения
    $a_lower = mb_strtolower($a, 'UTF-8');
    $b_lower = mb_strtolower($b, 'UTF-8');

    // Проверяем, начинается ли ключ с подстроки $str
    $startsWithA = mb_substr($a_lower, 0, mb_strlen($str_lower, 'UTF-8')) === $str_lower;
    $startsWithB = mb_substr($b_lower, 0, mb_strlen($str_lower, 'UTF-8')) === $str_lower;

    // Сортировка: сначала элементы, у которых ключ начинается с $str
    if ($startsWithA && $startsWithB) {
        return 0;
    }
    if ($startsWithA) {
        return -1;
    }
    if ($startsWithB) {
        return 1;
    }

    // Если оба ключа не начинаются с $str, оставляем их на своих местах
    return 0;
  });

  // Результат
  // print_r($sortedArray);
  dd($array);
  // MailService::sendError(env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$date.' || '.$url, ['testArray'])
  // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));

  // $xml = SitemapService::add('https://xn--80adplhnbnk0i.xn--p1ai/автовокзал/77/Москва', 'weekly', $xml);
  // $xml = SitemapService::add('https://xn--80adplhnbnk0i.xn--p1ai/расписание/77/Москва', 'weekly', $xml);
  // $xml = SitemapService::add('https://xn--80adplhnbnk0i.xn--p1ai/автовокзал/92/Севастополь', 'weekly', $xml);
  // $xml = SitemapService::add('https://xn--80adplhnbnk0i.xn--p1ai/расписание/92/Севастополь', 'weekly', $xml);
  // File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
  // FtpLoadingService::put();
  $dispatchPoints = DispatchPoint::where([['created_at', '>', date('Y-m-d', strtotime('-1 day'))]])->get()->ToArray();
  MailService::sendDump('Новые точки от e-traffic', '');
  dd($dispatchPoints);
  // // ini_set('max_execution_time', 600);

  $kladrs = Kladr::has('dispatchPoints')->orHas('arrivalPoints')->get();
  foreach($kladrs as $kladr){
    $kladr->slug = SlugService::create($kladr->name);
    $kladr->save();
  }
  dd($kladrs);
  $dispatchPoint = DispatchPoint::find(66690);
  // dd($dispatchPoint->kladr->id);
  // MailService::sendDump('Byaj', ['qwe']);
  // $xml = SitemapService::addOne('http://localhost:5173/расписание/123124/Майами1', 'daily');
  
  
  // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
        // Получаем пространство имен


  // dd($url);
  $sitemapPages = SitemapPage::all();
  $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
  foreach($sitemapPages as $page){
    $namespaces = $xml->getNamespaces(true);
    
    // Если пространство имен существует, добавляем его в XPath запрос
    if (isset($namespaces[''])) {
        // Добавляем пространство имен в XPath запрос
        $xml->registerXPathNamespace('sm', $namespaces['']);
        $url = $xml->xpath("//sm:url[sm:loc='$page->url']");
    } else {
        // Если пространства имен нет, просто выполняем запрос без него
        $url = $xml->xpath("//url[loc='$page->url']");
    }    
    if(!$url){
      $xml = SitemapService::add($page->url, $page->changefreq, $xml);
    }
  }
  
  
  File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
  FtpLoadingService::put();
  dd('');
  // dd('');
  $result = [];


  // $station = Station::find(10);
  // dd($station->address, $station->kladr->name, stripos($station->address, $station->kladr->name));

  $stations = Station::where([['address', '<>', null]])->get();
  // dd($stations);
  foreach($stations as $station){ 
    if(stripos($station->address, $station->kladr->name) === false){
      $station->address = $station->kladr->name.', '.$station->address;
      $station->save();
    }
    if(!$station->latitude || !$station->longitude){
      $geoCode = Http::get('https://geocode-maps.yandex.ru/1.x/?apikey=e40ec27a-8117-4ad6-9b72-649510a74f02&geocode='.($station->address).'&format=json')->object();
      if(isset($geoCode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos)){
          $pos = $geoCode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
          $coordinates = explode(' ', $pos);
          $station->latitude = $coordinates[1];
          $station->longitude = $coordinates[0];
          $station->save();
      }
    }
  }
  dd($stations);
  dd(stripos('фываыновфывафыва', 'ноф'));
  $geoCode = Http::get('https://geocode-maps.yandex.ru/1.x/?apikey=e40ec27a-8117-4ad6-9b72-649510a74f02&geocode=Новосибирск, Гусинобродское шоссе, 37/2&format=json')->object();
  $pos = $geoCode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
  $coordinates = explode(' ', $pos);
  dd($coordinates);
  $arrivalPoints = CacheArrivalPoint::with('dispatchPoint')->where([['created_at', '>', date('Y-m-d', strtotime('-1 day'))]])->get();
  
  $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
  // for($i = 0; $i < count($xml->url); $i++){
  //     Log::info($xml->url[$i]->loc.' '.$newLoc);
  //     if($xml->url[$i]->loc == $newLoc){
  //         return [
  //             'existing' => true
  //         ];
  //     }
  // }
  foreach($arrivalPoints as $arrivalPoint){
    $newNode = $xml->addChild('url');
    $newNode->addChild('loc', env('FRONTEND_URL').'/автобус/'.$arrivalPoint->dispatchPoint->slug.'/'.$arrivalPoint->slug);
    $newNode->addChild('lastmod', date('Y-m-d'));
    $newNode->addChild('changefreq', 'daily'); //weekly
    $newNode->addChild('priority', '1.0');    
  }

  
  File::put(env('XML_FILE_NAME'), $xml->asXML());
  // Log::info('sitemap3');
  FtpLoadingService::put();

  dd($arrivalPoints);
  // $stations = Station::where([[['created_at', '>', '2024-10-08 22:36:55']]]);
  // foreach($stations as $station){
  //   if(!$station->kladrStationPage){
  //     $stationPage = KladrStationPage::create([
  //         'name' => 'Автовокзал '.$station->name,
  //         'description' => 'Автовокзал '.$station->name.': расписание, справочная, билеты на автобус',
  //         'url_settlement_name' => SlugService::create($station->name),
  //         'url_region_code' => mb_strcut($station->kladr->code, 0, 2),
  //         'hidden' => false,
  //         'station_id' => $station->id,
  //     ]);
  //     SitemapService::add(env('FRONTEND_URL').'/автовокзал/'.$stationPage->url_region_code.'/'.$stationPage->url_settlement_name, 'weekly');
  //   }    
  // }
  $kladrs = Kladr::has('dispatchPoints')->orHas('arrivalPoints')->get();
  foreach($kladrs as $kladr){
    if(!$kladr->slug){
      $kladr->slug = SlugService::create($kladr->name); 
      $kladr->save();
    }
  }

  dd($kladrs);
  // $dispatchPoint = DispatchPoint::find(66690);
  // $dispatchPoint->kladr_id = 193127;
  // $dispatchPoint->save();
  // dd($arrivalPoints = CacheArrivalPoint::where([['created_at', '>', date('Y-m-d', strtotime('-1 day'))]])->get(););
  $pagesOnMainSetting = [];

  $kladrStationPages = KladrStationPage::with('kladr')->where([['kladr_id', '<>', null], ['hidden', '=', false]])->get();
  foreach($kladrStationPages as $kladrStationPage){
      $kladrStationPageArr = $kladrStationPage->toArray();
      $kladrStationPageArr['stationPages'] = KladrStationPage::with('station.kladr')->whereHas('station', function($query) use($kladrStationPage){
          $query->where([['kladr_id', '=', $kladrStationPage->kladr_id], ['hidden', '=', false]]);
      })->orderByDesc('id')->get()->toArray();

      // foreach($pagesOnMainSetting as $key => $region){
          usort($kladrStationPageArr['stationPages'], function($a, $b) {
              return strcmp($a['name'], $b['name']);
          });
          // $pagesOnMainSetting[$key] = $region;
      // }
      // ksort($pagesOnMainSetting);

      $pagesOnMainSetting[$kladrStationPage->kladr->region ? $kladrStationPage->kladr->region : 'Московская обл' ][] = $kladrStationPageArr;
  }

  foreach($pagesOnMainSetting as $key => $region){
      usort($region, function($a, $b) {
      return strcmp($a['name'], $b['name']);
      });
      foreach($region as $key1 => $settlement){
          // dd($settlement);
          if(substr($settlement['kladr']['code'], 5, 3) == '001'){
              $temp = $settlement;
              $region[$key1] = $region[0];
              $region[0] = $temp;
          }
      }
      $pagesOnMainSetting[$key] = $region;
  }
  
  dd($pagesOnMainSetting);

  // }
  // PagesOnMainService::recreate();
  // dd('');

  $files = ['img/a_ticket.pdf', 'img/ad.pdf'];
  $pdf = new Fpdi();

  foreach ($files as $file) {
      // set the source file and get the number of pages in the document
      $pageCount =  $pdf->setSourceFile($file);

      for ($i=0; $i < $pageCount; $i++) { 
          //create a page
          $pdf->AddPage();
          //import a page then get the id and will be used in the template
          $tplId = $pdf->importPage($i+1);
          //use the template of the imporated page
          $pdf->useTemplate($tplId);
      }
  }

  //return the generated PDF
  return $pdf->Output();   
  dd('');
  Mail::to('marsel.galimov.241@gmail.com')->send(new OtpMemberMail('123456'));
  dd(date('Y-m-d H:i:s'));
  $otp = OtpMember::find(4);

  // dd($otp->code);
  dd(Hash::check(4843301, $otp->code));
  dd(OtpMember::where('code', Hash::make(715139))->first());
  dd(User::where([['email', '=', 'marsel.galimov.241@gmail.com']])->whereHas('role', function(Builder $query){
    return $query->where([['name', '<>', 'Пользователь']]);
})->first());
  dd(User::where([['email', '=', 'marsel@mail.ru']])->whereHas('role', function(Builder $query){
        return $query->where([['name', '<>', 'Пользователь']]);
    })->first());
  Auth::loginUsingId(33);
  dd(Auth::user()->role);
  $dispatchPoints = DispatchPoint::all();

  foreach($dispatchPoints as $dispatchPoint){
    if($dispatchPoint->station){
      continue;
    }
    $station = Station::where([['name', '=', $dispatchPoint->name]])->first();
    if($station){
      $dispatchPoint->station_id = $station->id;
      $dispatchPoint->save();
    }

  }
  dd('');

  PagesOnMainService::recreate();
  dd('');
  $dispatchPoints = DispatchPoint::where([['station_id', '=', null]])->get();
  // dd($dispatchPoints);
  foreach($dispatchPoints as $dispatchPoint){
      if(!$dispatchPoint->kladr){
          continue;
      }
      $kladr = $dispatchPoint->kladr;
      $station = Station::where([['kladr_id', '=', $kladr->id], ['name', '=', $dispatchPoint->name]])->first();
      if(!$station){
          $station = Station::create([
              'name' => $dispatchPoint->name,
              'kladr_id' => $kladr->id
          ]);
      }
  
      $kladrPage = $kladr->kladrStationPage;
      
      if(!$kladrPage){
          $kladrPage = KladrStationPage::create([
              'name' => 'Автовокзалы и автостанции '.$kladr->name,
              'description' => $kladr->name.' Автовокзалы и автостанции: расписание, справочная, билеты на автобус',
              'url_settlement_name' => SlugService::create($kladr->name),
              'url_region_code' => mb_strcut($kladr->code, 0, 2),
              'hidden' => false,
              'kladr_id' => $kladr->id,
          ]);
          // SitemapService::add(env('FRONTEND_URL').'/расписание/'.$kladrPage->url_region_code.'/'.$kladrPage->url_settlement_name, 'weekly');
      }
      if(!$station->kladrStationPage){
          $stationPage = KladrStationPage::create([
              'name' => 'Автовокзал '.$station->name,
              'description' => 'Автовокзал '.$station->name.': расписание, справочная, билеты на автобус',
              'url_settlement_name' => SlugService::create($station->name),
              'url_region_code' => mb_strcut($kladr->code, 0, 2),
              'hidden' => false,
              'station_id' => $station->id,
          ]);
          // SitemapService::add(env('FRONTEND_URL').'/автовокзал/'.$stationPage->url_region_code.'/'.$stationPage->url_settlement_name, 'weekly');
      }
  }
  dd('');
  
  $xml = simplexml_load_file(env('XML_FILE_NAME'));

// Указываем правильный пространственный идентификатор
$namespaces = $xml->getNamespaces(true);
$xml->registerXPathNamespace('sm', $namespaces['']);


// Используем XPath для выборки всех <loc> элементов
$urls = $xml->xpath('//sm:url[starts-with(sm:loc, "https://xn--80adplhnbnk0i.xn--p1ai/автобус/Томск")]/sm:loc');

  dd($urls);
  // $result = $xml->xpath('/xml/urlset/url/loc');
  // dd($result);
  $arr = [];
  for($i = 0; $i < count($xml->url); $i++){
    if(str_starts_with((string)$xml->url[$i]->loc, 'https://xn--80adplhnbnk0i.xn--p1ai/автобус/Томск')){
      $locArr = explode('/', (string)$xml->url[$i]->loc);
      // dd($locArr);
      $arr[] = [(string)$xml->url[$i]->loc, $locArr[4], $locArr[5]];
    }
  }
  dd($arr);
  // FtpLoadingService::put();
  // dd('');
  // dd();
  date_default_timezone_set('Europe/Moscow');
  $sitemap = simplexml_load_string(Storage::disk('sftp')->get('/var/www/rosvokzaly/data/public/sitemap.xml'));
  
  $sitemap[0]->sitemap->lastmod = date('c');
  Storage::disk('sftp')->put('/var/www/rosvokzaly/data/public/sitemap.xml', $sitemap->asXML());
  dd('');
//   Storage::disk('sftp')->put('/var/www/rosvokzaly/data/public/sitemap.xml', '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
// <script/>
// <sitemap>
// <loc>https://xn--80adplhnbnk0i.xn--p1ai/sitemaps/directions.xml.gz</loc>
// <lastmod>2024-10-04T22:28:51+03:00</lastmod>
// <changefreq>daily</changefreq>
// </sitemap>
// </sitemapindex>');
dd('');
  // dd($sitemap);
  // dd(Kladr::find(1770206));

  // for($i = 0; $i < count($xml->url); $i++){
  //     Log::info($xml->url[$i]->loc.' '.$newLoc);
  //     if($xml->url[$i]->loc == $newLoc){
  //         return [
  //             'existing' => true
  //         ];
  //     }
  // }
  // $newNode = $xml->addChild('url');
  // $newNode->addChild('loc', $newLoc);
  // $newNode->addChild('lastmod', date('Y-m-d'));
  // $newNode->addChild('changefreq', $changefreq); //weekly
  // $newNode->addChild('priority', '1.0');
  
  // File::put(env('XML_FILE_NAME'), $xml->asXML());
    $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));

    $dispatchData = PointService::dispatchKandE();
    foreach($dispatchData as $dispatch){
      if(array_key_exists('details', $dispatch)){
        $arrivalData = PointService::kAndE($dispatch['id']);
      }
      else{
        // dd($dispatch);
        // dd(Kladr::find($dispatch['id'])->dispatchPoints);
        // if(!Kladr::find($dispatch['id'])){
        //   dd($dispatch);
        // }
        $dispatchPoints = Kladr::find($dispatch['id'])->dispatchPoints;
        // dd(Kladr::find($dispatchPoints));
        $arrivalData = [];
        foreach($dispatchPoints as $dispatchPoint){
            $x = PointService::kAndE($dispatchPoint['id']);
            $name_a = array_column($arrivalData, 'name');
            $region_a = array_column($arrivalData, 'region');



            // Удаляем элементы с повторяющимися name и region
            $x = array_filter($x, function($item) use ($name_a, $region_a) {
                return !in_array($item['name'], $name_a) && !in_array($item['region'], $region_a);
            });

            // Объединяем два массива
            $arrivalData = array_merge($arrivalData, $x);                

            // $result = array_merge($result, PointService::kAndE($dispatchPoint->id));
        }
      }
      if($arrivalData){
        foreach($arrivalData as $arrival){
          // SitemapService::add(env('FRONTEND_URL').'/автобус/'.$dispatch->slug.'/'.$arrival->slug, 'dayly');

          $newNode = $xml->addChild('url');
          $newNode->addChild('loc', env('FRONTEND_URL').'/автобус/'.$dispatch['slug'].'/'.$arrival['slug']);
          $newNode->addChild('lastmod', date('c'));
          $newNode->addChild('changefreq', 'daily'); //weekly
          $newNode->addChild('priority', '1.0');
        }          
      }
    }
    File::put(env('XML_FILE_NAME'), $xml->asXML());
    FtpLoadingService::put();
    dd("");
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
