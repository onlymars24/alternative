<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MessagesExport implements FromArray, WithColumnWidths, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $messagesType;
    protected $messages;
    protected $period;
    protected $types;
    protected $statuses;

    public function __construct($messagesType, $messages, $period, $types, $statuses)
    {
        $this->messagesType = $messagesType;
        $this->messages = $messages;
        $this->period = $period;
        $this->types = $types;
        $this->statuses = $statuses;
    }

    public function array(): array
    {
        $messages = [];
        $messages[] = ['Список сообщений '.$this->messagesType];
        $messages[] = ['От', $this->period[0] ];
        $messages[] = ['До', $this->period[1] ];
        $messages[] = [''];
        if($this->messagesType == 'whatsapp'){
            $messages[] = ['ID', 'Дата и время отправления', 'Телефон', 'Статус', 'Тип'];
            foreach($this->messages as $message){
                $messages[] = [$message->id, $message->created_at, $message->phone, $this->statuses[$message->status]['label'], $this->types[$message->type]['label']];
            }
        }
        if($this->messagesType == 'sms'){
            $messages[] = ['ID', 'Дата и время отправления', 'Телефон', 'Стоимость сообщения', 'Баланс после отправки', 'Статус'];
            foreach($this->messages as $message){
                $messages[] = [$message->id, $message->created_at, $message->phone, $message->cost, $message->balance, $this->statuses[$message->status]['label']];
            }
        }
        // Log::info(json_encode($messages));
        return $messages;
    }
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 30,  
            'C' => 20,
            'D' => 30,
            'E' => 30, 
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
                ($this->messagesType == 'sms' ? 'A5:F5' : 'A5:E5') => [    'borders' => [
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
