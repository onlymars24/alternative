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

        $kladrs = Kladr::where([['code', 'like', '%00']])->get();
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