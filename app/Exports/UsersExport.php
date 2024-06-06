<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromArray, WithColumnWidths, WithStyles
{

    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }
    public function array(): array
    {

        $users = [];
        $users[] = ['Список пользователей' ];

        $users[] = ['ID', 'Телефон', 'Бонусы',];
        foreach($this->users as $user){
            $users[] = [$user->id, $user->phone, $user->bonuses_balance	];
        }

        return $users;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 19,
            'B' => 20,  
            'C' => 20,
            'D' => 18,
            'E' => 16, 
            'F' => 16,  
            'G' => 13,  
            'H' => 18,  
            'I' => 17,  
            'J' => 12,  
            'K' => 12,  
            'L' => 15,  
            'M' => 13,  
            'N' => 13,  
            'O' => 13,  
            'P' => 12,  
            'Q' => 11,  
            'R' => 12,   
            'S' => 10,    
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $pageSetup = new PageSetup;
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A3);
        $pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setPageSetup($pageSetup);
        return [
                'A1' => [ 'font' => 
                [
                    'bold' => true,
                ],
                ],
                'A2:C2' => [    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],       
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'EEECE1',
                        ]    
                    ],             
                ],
        ];
    }
}
