<?php

namespace Database\Seeders;

use App\Models\Kladr;
use XBase\TableReader;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertKladr extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = new TableReader(
        public_path('KLADR.DBF'),
        [
            'encoding' => 'cp866'
        ]
        );
        $i = 0;
        while ($record = $table->nextRecord()) {
        // $i++;
        // if($record->get('name') == '3-е отделение'){
            // if($record->get('name') == 'Краснодарский' || $record->get('name') == 'Краснодар'){
            // while($record = $table->nextRecord() && $record->get('socr') != 'обл'){
            Kladr::create([
                'name' => $record->get('name'), 
                'socr' => $record->get('socr'), 
                'code' => $record->get('code'), 
                'index' => $record->get('index'), 
                'gninmb' => $record->get('gninmb'), 
                'uno' => $record->get('uno'), 
                'ocatd' => $record->get('ocatd'), 
                'status' => $record->get('status'), 
            ]);
            // } 
        // }
        
        //or
        // echo $record->my_column;
        }
    }
}
