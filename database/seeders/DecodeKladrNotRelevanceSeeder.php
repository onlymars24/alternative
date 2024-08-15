<?php

namespace Database\Seeders;

use App\Models\Kladr;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DecodeKladrNotRelevanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kladrs = Kladr::where([['code', 'not like', '%00']])->get();
        $output = new ConsoleOutput();
        $totalItems = $kladrs->count(); // Замените на общее количество записей, которые вы собираетесь создать.
        $progressBar = new ProgressBar($output, $totalItems);

        $progressBar->start();

        foreach($kladrs as $kladr){
            $relevance = mb_strcut($kladr->code, 11, 2);

            if($relevance == '00'){
                $kladr->relevance = 'Актуальный объект';
            }
            elseif($relevance >= '01' && $relevance <= '50'){
                $kladr->relevance = 'Объект был переименован';
            }
            elseif($relevance == '51'){
                $kladr->relevance = 'Объект был переподчинён';
            }
            elseif($relevance >= '52' && $relevance <= '98'){
                $kladr->relevance = 'Резервное значение признака актуальности';
            }
            elseif($relevance == '99'){
                $kladr->relevance = 'Адресный объект не существует';
            }
            $kladr->save();
            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln('Seeder completed!');
    }
}
