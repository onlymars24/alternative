<?php

namespace Database\Seeders;

use App\Models\Kladr;
use Nette\Utils\DateTime;
use App\Models\KladrsCouple;
use Illuminate\Database\Seeder;
use App\Services\VkMarketService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertVkMarketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kladrId = 221627;
        $kladr = Kladr::find($kladrId);
        if(!$kladr->album_id){
          $title = 'Автовокзалы и автостанции '.$kladr->name;
          $response = Http::get(env('VK_URL').'/market.addAlbum?owner_id='.-env('VK_GROUP_ID').'
          &v=5.131
          &group_id='
          .env('VK_GROUP_ID').'&access_token='.env('VK_TOKEN').'&title='.$title)->object();
          if(isset($response->response->market_album_id)){
              $kladr->album_id = $response->response->market_album_id;
              $kladr->save();
          }          
        }


        $kladrsCouples = KladrsCouple::with('dispatchKladr', 'arrivalKladr')->where([['dispatch_kladr_id', '=', $kladrId]])->get();

        $output = new ConsoleOutput();
        $totalItems = $kladrsCouples->count(); // Замените на общее количество записей, которые вы собираетесь создать.
        $progressBar = new ProgressBar($output, $totalItems);
        $progressBar->start();

        foreach($kladrsCouples as $couple){
          $dispatchKladr = $couple->dispatchKladr;
          $arrivalKladr = $couple->arrivalKladr;
  
          if($dispatchKladr && $arrivalKladr){
            $couple->market_id = VkMarketService::marketAdd($dispatchKladr, $arrivalKladr);
            $couple->save();
          }
          $progressBar->advance();
        }
        $progressBar->finish();
        $output->writeln('Seeder completed!');
    }
}
