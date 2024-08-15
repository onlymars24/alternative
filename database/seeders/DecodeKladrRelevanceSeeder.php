<?php

namespace Database\Seeders;

use App\Models\Kladr;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class DecodeKladrRelevanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 250000; $i+=49000){
            $kladrs = Kladr::where([['id', '>', $i], ['code', 'like', '%00']])->take(50000)->get();
            $output = new ConsoleOutput();
            $totalItems = $kladrs->count(); // Замените на общее количество записей, которые вы собираетесь создать.
            $progressBar = new ProgressBar($output, $totalItems);

            $progressBar->start();

            foreach($kladrs as $kladr){
                $kladr->relevance = 'Актуальный объект';
                $kladr->save();
                $progressBar->advance();
            }

            $progressBar->finish();


            $output->writeln('Seeder completed!');
        }

    }
}