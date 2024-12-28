<?php

use App\Models\Kladr;
use App\Models\Station;
use App\Models\CacheRace;
use App\Models\SitemapPage;
use FontLib\Table\Type\post;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Services\SlugService;
use App\Services\KladrService;
// use App\Http\Controllers\Api\RaceController;
use App\Services\PointService;
use App\Models\KladrStationPage;
use App\Services\SitemapService;
use App\Models\CacheArrivalPoint;
use Illuminate\Support\Facades\DB;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\KladrController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ArrivalController;
use App\Http\Controllers\BonusesController;
use App\Http\Controllers\FixUserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\Api\DateController;
use App\Http\Controllers\BusRouteController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PageMainController;
use App\Http\Controllers\RacesXmlController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AllPointsController;
use App\Http\Controllers\Api\RacesController;
use App\Http\Controllers\DebuggingController;
use App\Http\Controllers\NewPointsController;
use App\Http\Controllers\RobotsTxtController;
use App\Http\Controllers\BusStationController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\PassengersController;
use App\Http\Controllers\RacesCacheController;
use App\Http\Controllers\ReturnRaceController;
use App\Http\Controllers\Api\SendSmsController;
use App\Http\Controllers\CustomKladrController;
use App\Http\Controllers\PageSitemapController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Api\CountriesController;
use App\Http\Controllers\PopularPointsController;
use App\Http\Controllers\AdvertisingPdfController;
use App\Http\Controllers\MainPageMetaImgController;
use App\Http\Controllers\KladrStationPageController;
use App\Http\Controllers\Api\ArrivalPointsController;
use App\Http\Controllers\PageUpcomingTripsController;
use App\Http\Controllers\RacesExistingMailController;
use App\Http\Controllers\Api\DispatchPointsController;
use App\Http\Controllers\DispatchArrivalSelectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/export/excel/messages', [MessageController::class, 'exportExcel'])->name('export.excel');

Route::get('/sitemap/reload', function (Request $request) {
    dd('');
    ini_set('max_execution_time', 600);

    $sitemapPath = public_path(env('XML_FILE_NAME'));
    File::put($sitemapPath, '<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    </urlset>');
    FtpLoadingService::put();

    DB::table('sitemap_pages')->delete();
    $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
  
   
  
  
    $pages = KladrStationPage::all();
    foreach($pages as $page){
      $result[] = env('FRONTEND_URL').'/'.($page->kladr_id ? 'расписание' : 'автовокзал').'/'.$page->url_region_code.'/'.$page->url_settlement_name;
    }
    // dd($res);
    // $result = [];
    $dispatchData = PointService::dispatchData();
    foreach($dispatchData as $dispatchItem){
      $arrivalData = PointService::arrivalDataBySourceId($dispatchItem->sourceId);
      foreach($arrivalData as $arrivalItem){
        $result[env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug']] = env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug'];
      }
    }
    // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
    foreach($result as $item){
      $xml = SitemapService::add($item, stripos($item, 'автобус') === false ? 'weekly' : 'daily', $xml);
    }
    File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
    FtpLoadingService::put();
    // dd($xml);  
});

Route::get('/new/points', [NewPointsController::class, 'get']);
Route::middleware('auth:sanctum')->group(function(){

    

    
    Route::middleware('admin')->group(function(){
        Route::post('/tickets/paginate', [TicketController::class, 'paginate']);
        Route::get('/tickets/reports', [TicketController::class, 'reports']);

        Route::post('/debugging', [DebuggingController::class, 'get']);

        Route::get('/roles/admins', [RoleController::class, 'admins']);
        Route::get('/roles/editors', [RoleController::class, 'editors']);
        Route::post('/roles/editors/add', [RoleController::class, 'editorsAdd']);
        Route::post('/roles/editors/delete', [RoleController::class, 'editorsDelete']);

        Route::get('/order', [OrderController::class, 'one']);
        Route::post('/settings/cluster/due', [SettingsController::class, 'setClusterDue']);

        Route::get('/expenses', [ExpensesController::class, 'all']);
        Route::post('/expense/create', [ExpensesController::class, 'create']);
        Route::post('/expense/delete', [ExpensesController::class, 'delete']);
        Route::post('/expense', [ExpensesController::class, 'one']);
    
        Route::get('/dues', [SettingsController::class, 'getDues']);
        Route::post('/dues/set', [SettingsController::class, 'setDue']);

        Route::post('/ad/pdf/upload', [AdvertisingPdfController::class, 'upload']);
        Route::post('/ad/pdf/delete', [AdvertisingPdfController::class, 'delete']);
        Route::get('/ad/pdf', [AdvertisingPdfController::class, 'get']);

        Route::post('/main/page/meta/img/upload', [MainPageMetaImgController::class, 'upload']);
        Route::post('/main/page/meta/img/delete', [MainPageMetaImgController::class, 'delete']);

        Route::post('/races/xml/create', [RacesXmlController::class, 'create']);
        Route::post('/admin/ticket/return', [TicketController::class, 'getBack']);
    });
    
    Route::middleware('user.has.role:Администратор,Редактор')->group(function(){
        Route::get('/member', [MemberAuthController::class, 'member']);
        
        Route::get('/kladr/station/pages', [KladrStationPageController::class, 'all']);
        Route::get('/kladr/station/page/id', [KladrStationPageController::class, 'oneById']);
        Route::post('/kladr/station/page/create', [KladrStationPageController::class, 'create']);
        Route::post('/kladr/station/page/edit', [KladrStationPageController::class, 'edit']);
        Route::post('/kladr/station/page/delete', [KladrStationPageController::class, 'delete']);
        Route::get('/kladr/station/pages/kladr', [KladrStationPageController::class, 'kladrPages']);

        Route::post('/kladr/station/page/image/upload', [KladrStationPageController::class, 'imageUpload']);
        Route::post('/kladr/station/page/image/delete', [KladrStationPageController::class, 'imageDelete']);

        Route::post('/kladr/station/page/meta/image/upload', [KladrStationPageController::class, 'imageMetaUpload']);
        Route::post('/kladr/station/page/meta/image/delete', [KladrStationPageController::class, 'imageMetaDelete']);
        
        Route::get('/stations', [StationController::class, 'all']);
        Route::post('/station/create', [StationController::class, 'create']);
        Route::post('/station/edit', [StationController::class, 'edit']);
        Route::post('/station/delete', [StationController::class, 'delete']);
        Route::get('/station/id', [StationController::class, 'oneById']);

        

        Route::post('/station/add/to/dispatch/point', [StationController::class, 'addToDispatchPoint']);
        Route::post('/station/add/to/arrival/point', [StationController::class, 'addToArrivalPoint']);
        
        Route::post('/kladrs/connected', [KladrController::class, 'allConnected']);

        Route::get('/kladrs/connected', [KladrController::class, 'allConnected']);
    
        Route::get('/kladrs', [KladrController::class, 'all']);

        Route::post('/kladrs/dispatch/add', [KladrController::class, 'addDispatch']);
        Route::post('/kladrs/arrival/add', [KladrController::class, 'addArrival']);

        Route::post('/page/main', [PageMainController::class, 'edit']);

        Route::get('/robots/txt', [RobotsTxtController::class, 'get']);
        Route::post('/robots/txt', [RobotsTxtController::class, 'edit']);

        Route::post('/page/upcoming/trips', [PageUpcomingTripsController::class, 'edit']);

        Route::get('/get/feedback', [FeedbackController::class, 'get']);
        
        Route::get('/bus/routes', [BusRouteController::class, 'all']);
        Route::post('/bus/route/create', [BusRouteController::class, 'create']);
        Route::post('/bus/route/edit', [BusRouteController::class, 'edit']);
        Route::post('/bus/route/delete', [BusRouteController::class, 'delete']);
        Route::post('/popular/points/edit', [PopularPointsController::class, 'edit']);

        
        Route::post('/new/points', [NewPointsController::class, 'add']);

        Route::get('/bonuses/transactions', [BonusesController::class, 'transactions']);
        Route::post('/bonuses/plus', [BonusesController::class, 'plus']);
        Route::post('/bonuses/minus', [BonusesController::class, 'minus']);
        Route::get('/users', [AuthController::class, 'users']);

        // Route::get('/sms/all', [SmsController::class, 'getAll']);
        // Route::get('/sms/whatsapp/all', [SmsController::class, 'getWhatsAppAll']);  
        
        Route::post('/messages', [MessageController::class, 'filter']);  

        Route::get('/points', [AllPointsController::class, 'all']);
        Route::get('/matches', [MatchController::class, 'all']);

        Route::post('/match/create', [MatchController::class, 'create']);

        Route::post('/match/delete', [MatchController::class, 'delete']);

        Route::post('/match/replacement', [MatchController::class, 'replacement']);

        Route::get('/custom/kladrs/filter', [CustomKladrController::class, 'filter']);
        Route::post('/custom/kladrs/create', [CustomKladrController::class, 'create']);
        Route::post('/custom/kladrs/edit', [CustomKladrController::class, 'edit']);
        Route::get('/custom/kladrs/one', [CustomKladrController::class, 'one']);
        Route::post('/custom/kladrs/delete', [CustomKladrController::class, 'delete']);
    });
    
    Route::post('/member/logout', [MemberAuthController::class, 'logout']);
});

Route::get('/main/page/meta/img', [MainPageMetaImgController::class, 'get']);

Route::get('/sitemap/page/check', [PageSitemapController::class, 'checkUrl']);

Route::post('/member/send/code', [MemberAuthController::class, 'sendCode'])->middleware('throttle:2,1');
Route::post('/member/confirm/code', [MemberAuthController::class, 'confirmCode']);



Route::middleware('auth:api')->group(function(){
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/edit/email', [AuthController::class, 'editEmail']);
    
    Route::get('/orders', [OrderController::class, 'all']);
    Route::get('/passengers', [PassengersController::class, 'all']);
    Route::post('/passenger/delete', [PassengersController::class, 'delete']);
    Route::post('/passenger/edit', [PassengersController::class, 'edit']);
    Route::post('/passenger/save', [PassengersController::class, 'save']);
    Route::post('/ticket/return', [TicketController::class, 'getBack']);

    Route::post('/order/return', [OrderController::class, 'getBack']);
    Route::get('/order/tickets', [TicketController::class, 'orderTickets']);
    // Route::get('/tickets', [TicketController::class, 'all']);

    // Route::post('/tickets/paginate', [TicketController::class, 'paginate']);
});

Route::get('/routes/station', [RouteController::class, 'getByStation']);
Route::get('/routes/kladr', [RouteController::class, 'getByKladr']);
Route::get('/routes/kladrs/couple', [RouteController::class, 'getByKladrsCouple']);

Route::get('/bus/route', [BusRouteController::class, 'one']);

Route::post('/send/sms/auth', [AuthController::class, 'sendSmsAuth']);
Route::get('/confirm/sms/auth', [AuthController::class, 'confirmAuth']);

Route::post('/send/feedback', [FeedbackController::class, 'send']);

Route::get('/unfixed/user', [FixUserController::class, 'unfixedUser']);
Route::post('/fix/user/sms', [FixUserController::class, 'smsSend']);
Route::get('/fix/user/sms', [FixUserController::class, 'smsGet']);
Route::post('/fix/user', [FixUserController::class, 'fix']);

Route::post('/order/book', [OrderController::class, 'book']);

Route::post('/order/transactions', [TransactionController::class, 'all']);

Route::get('/race', [RaceController::class, 'get']);

Route::post('/send/race/existing', [RacesExistingMailController::class, 'send']);
Route::post('/return/race/send', [ReturnRaceController::class, 'sendReturnRace']);


Route::get('/settings/bonuses/percent', [SettingsController::class, 'getBonusesPercent']);
Route::get('/settings/cluster/due', [SettingsController::class, 'getClusterDue']);

// Route::post('/admin/login', [AdminController::class, 'login']);

// Route::get('/get/feedback', [FeedbackController::class, 'get']);

Route::get('/station/arrival/kladrs', [StationController::class, 'races']);
Route::get('/kladr/arrival/kladrs', [KladrController::class, 'races']);

Route::get('/kladr/links', [KladrController::class, 'links']);

Route::get('/kladr/station/pages/station', [KladrStationPageController::class, 'stationPages']);

Route::get('/main/pages', [PageMainController::class, 'mainPages']);
Route::get('/page/main', [PageMainController::class, 'get']);


Route::get('/page/upcoming/trips', [PageUpcomingTripsController::class, 'get']);







Route::get('/kladr/station/page', [KladrStationPageController::class, 'one']);




// Route::get('/bus/stations/main', [KladrStationPageController::class, 'main']);



Route::get('/kladr/station/page/station/old', [KladrStationPageController::class, 'oneOld']);













// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);






// Route::post('/sms/reset', [SmsController::class, 'sendReset']);
// Route::get('/sms/reset', [SmsController::class, 'getReset']);
// Route::post('/sms/register', [SmsController::class, 'sendRegister']);
// Route::get('/sms/register', [SmsController::class, 'getRegister']);
// Route::post('/reset', [AuthController::class, 'reset']);







Route::get('/cache/races', [RacesCacheController::class, 'get']);


// НОВОСТИ В РАЗРАБОТКЕ!!!! НЕ ТРОГАТЬ!!!!!!!
// Route::get('/events', [EventController::class, 'all']);
// Route::get('/station/events', [EventController::class, 'stationOnes']);
// Route::get('/event', [EventController::class, 'one']);
// Route::post('/event/create', [EventController::class, 'create']);
// Route::post('/event/edit', [EventController::class, 'edit']);
// Route::post('/event/delete', [EventController::class, 'delete']);
// Route::post('/event/add/station', [EventController::class, 'addStation']);
// Route::post('/event/delete/station', [EventController::class, 'deleteStation']);



Route::get('/bonuses/user', [BonusesController::class, 'user']);

Route::get('/seven/days/races', [RaceController::class, 'sevenDaysRaces']);

Route::get('/races/simple', [RaceController::class, 'simpleRaces']);

Route::get('/races', [RaceController::class, 'races']);








Route::get('/arrival/points', [ArrivalController::class, 'all']);
Route::post('/arrival/points/paginate', [ArrivalController::class, 'paginate']);


Route::get('/dispatch/points', [DispatchController::class, 'all']);
Route::post('/dispatch/points/paginate', [DispatchController::class, 'paginate']);

Route::get('/dispatch/data', [DispatchArrivalSelectController::class, 'selectDispatch']);
Route::get('/arrival/data', [DispatchArrivalSelectController::class, 'selectArrival']);

Route::get('/dispatch/arrival/check', [DispatchArrivalSelectController::class, 'checkUrlParams']);

Route::apiResources([
    'dispatch_points' => DispatchPointsController::class,
    'arrival_points' => ArrivalPointsController::class,
    // 'races' => RacesController::class,
    // 'race' => RaceController::class,
    'date' => DateController::class,
    'countries' => CountriesController::class,
    'sms' => SendSmsController::class
]);