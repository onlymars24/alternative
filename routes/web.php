<?php

use App\MyApp;
// use PDFMerger;
use App\Models\Sms;
use App\Models\Role;
use App\Models\User;
use App\Models\Bonus;
use App\Models\Kladr;
use App\Models\Order;
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
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\KladrController;
use App\Http\Controllers\OrderController;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UsersExportController;
use App\Models\SitemapPage;
use Maatwebsite\Excel\Concerns\ToArray;

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


Route::get('/spread', function (Request $request) {

  // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));

  // $xml = SitemapService::add('https://xn--80adplhnbnk0i.xn--p1ai/автовокзал/77/Москва', 'weekly', $xml);
  // $xml = SitemapService::add('https://xn--80adplhnbnk0i.xn--p1ai/расписание/77/Москва', 'weekly', $xml);
  // $xml = SitemapService::add('https://xn--80adplhnbnk0i.xn--p1ai/автовокзал/92/Севастополь', 'weekly', $xml);
  // $xml = SitemapService::add('https://xn--80adplhnbnk0i.xn--p1ai/расписание/92/Севастополь', 'weekly', $xml);
  // File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
  // FtpLoadingService::put();


  $dispatchPoints = DispatchPoint::where([['created_at', '>', date('Y-m-d', strtotime('-1 day'))]])->get()->ToArray();
  MailService::sendDump('Новые точки от e-traffic', $dispatchPoints);
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
