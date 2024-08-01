<?php

namespace Database\Seeders;

use App\Models\Kladr;
use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use App\Models\CacheArrivalPoint;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConnectKladrsToPointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $points = CacheArrivalPoint::where([
            ['kladr_id', '=', null]
        ])->get();

        $output = new ConsoleOutput();
        $totalItems = $points->count(); // Замените на общее количество записей, которые вы собираетесь создать.
        $progressBar = new ProgressBar($output, $totalItems);

        $progressBar->start();






        foreach($points as $point){
            if(!$point->region){
                continue;
            }
            $whereArr = [
                ['name', '=', $point->name],
                ['region', '=', $point->region],
                ['code', 'like', '%00']
            ];

            if(str_contains($point->details, 'р-н')){
                $whereArr[] = ['district', '=', $point->details];
            }

            $kladrs = Kladr::where($whereArr)->get();
            if($kladrs->count() == 1){
                $point->kladr_id = $kladrs[0]->id;
                $point->save();
            }
            else{
                $query = "INSTR('$point->name', name) > 0 AND INSTR('$point->region', region) > 0";
                if(str_contains($point->details, 'р-н')){
                    $query = $query." AND INSTR('$point->details', district) > 0";
                }
                $kladrs = Kladr::whereRaw($query)->get();
                if($kladrs->count() == 1){
                    $point->kladr_id = $kladrs[0]->id;
                    $point->save();
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        $output->writeln('Seeder completed!');

    }
}