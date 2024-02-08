<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromArray, WithColumnWidths, WithStyles
{

    protected $tickets;
    protected $request;

    public function __construct($tickets, $request)
    {
        $this->tickets = $tickets;
        $this->request = $request;
    }
    public function array(): array
    {
        $ticketStatuses = [
            'B' => [
                'label' => 'Забронирован',
                'value' => 'B',
            ],
            'C' => [
                'label' => 'Отменён',
                'value' => 'C',
            ],
            'R' => [
                'label' => 'Возвращён',
                'value' => 'R',
            ],
            'S' => [
                'label' => 'Оплачен',
                'value' => 'S',
            ],
        ];
        // dd($salesSupplierFares);
        $tickets = [];
        $tickets[] = ['Статистика продаж' ];
        $tickets[] = ['От', $this->request->comparingDate1 ];
        $tickets[] = ['До', $this->request->comparingDate2 ];
        $tickets[] = [''];
        $tickets[] = ['', 'Количество', 'Тариф', 'Сбор 
автовокзала', 'Сбор агента', 'Итого', 'Комиссия сайта'];
        
        $tickets[] = ['Продажа билетов', $this->request->salesAmount, $this->request->salesSupplierFares, $this->request->salesSupplierDues, $this->request->salesDues, $this->request->salesTotal, $this->request->salesSiteCommission, ];
        $tickets[] = ['Возврат', $this->request->returnsAmount, '', $this->request->returnsSupplierDues, $this->request->returnsDues, $this->request->repayments, $this->request->returnsSiteCommission ];

        $tickets[] = ['Продажа пассажирских билетов', $this->request->salesPassengerAmount, $this->request->salesPassengerSupplierFares, $this->request->salesPassengerSupplierDues, $this->request->salesPassengerDues, $this->request->salesPassengerTotal, $this->request->salesPassengerSiteCommission, ];
        $tickets[] = ['Возврат пассажирских билетов', $this->request->returnsPassengerAmount, '', $this->request->returnsPassengerSupplierDues, $this->request->returnsPassengerDues, $this->request->repaymentsPassenger, $this->request->returnsPassengerSiteCommission ];

        $tickets[] = ['Продажа багажных билетов', $this->request->salesLuggageAmount, $this->request->salesLuggageSupplierFares, $this->request->salesLuggageSupplierDues, $this->request->salesLuggageDues, $this->request->salesLuggageTotal, $this->request->salesLuggageSiteCommission, ];
        $tickets[] = ['Возврат багажных билетов', $this->request->returnsLuggageAmount, '', $this->request->returnsLuggageSupplierDues, $this->request->returnsLuggageDues, $this->request->repaymentsLuggage, $this->request->returnsLuggageSiteCommission ];


        $tickets[] = ['Удержание', '', $this->request->holds, $this->request->holdsSupplierDues, $this->request->holdsDues, $this->request->holdsTotal, $this->request->holdsSiteCommission ];
        $tickets[] = ['Сумма для E-traffic', '', '', '', '', $this->request->eTrafficTotal, ''];    
        $tickets[] = [''];  
        $tickets[] = ['', 'Количество страховок', 'Стоимость страховок'];
        $tickets[] = ['Продажа страховок', $this->request->salesInsurancesAmount, $this->request->salesInsurancesPrice];
        $tickets[] = ['Возврат', $this->request->returnsInsurancesAmount, $this->request->returnsInsurancesPrice];
        $tickets[] = ['']; 
        $tickets[] = [
'Дата и время отправления
(местное)', 'Дата и время брони
(GMT +3)', 'Дата и время возврата
(GMT +3)', 'Часовой 
пояс', 'Номер 
билета', 'ID заказа', 'Пункт 
отправления', 'Пункт прибытия', 'Фамилия', 'Имя', 'Отчество', 'Статус рейса', 'Статус 
билета', 'Стоимость', 'Сумма возврата', 'Сбор 
поставщика', 'Сбор 
агента', 'Удержание', 'Комиссия 
сайта', 'Страхование', 'Цена страховки'];
        foreach($this->tickets as $ticket){
            $tickets[] = [$ticket->dispatchDate, $ticket->created_at, $ticket->status == 'R' ? $ticket->updated_at : null, $ticket->timezone, $ticket->ticketNum, $ticket->order_id, $ticket->dispatchStation, $ticket->arrivalStation, $ticket->lastName, $ticket->firstName, $ticket->middleName, $ticket->raceCancelled ? 'Отменён' : 'Не отменён', $ticketStatuses[$ticket->status]['label'], ($ticket->created_at > $this->request->comparingDate1 && $ticket->created_at < $this->request->comparingDate2) ? $ticket->price : '0', ($ticket->status == 'R' && $ticket->updated_at > $this->request->comparingDate1 && $ticket->updated_at < $this->request->comparingDate2) ? $ticket->repayment : '0', $ticket->supplierDues, $ticket->dues, ($ticket->status == 'R' && $ticket->updated_at > $this->request->comparingDate1 && $ticket->updated_at < $this->request->comparingDate2) ? (string)($ticket->price - $ticket->repayment) : '0', $ticket->duePrice, $ticket->insurance ? 'Застрахован' : 'Не застрахован', $ticket->insurance ? json_decode($ticket->insurance)->rate[0]->value : '0'];
        }

        return $tickets;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 19,
            'B' => 20,  
            'C' => 18,
            'D' => 16, 
            'E' => 16,  
            'F' => 13,  
            'G' => 18,  
            'H' => 17,  
            'I' => 12,  
            'J' => 12,  
            'K' => 15,  
            'L' => 13,  
            'M' => 13,  
            'N' => 13,  
            'O' => 12,  
            'P' => 11,  
            'Q' => 12,   
            'R' => 10,    
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
                'A5:G5' => [    'borders' => [
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
                'A6:G13' => [    'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],],
                'A6:A13' => [ 'font' => 
                [
                    'bold' => true,
                ],
                ],
                'B5:H5' => [ 'font' => 
                [
                    'bold' => true,
                ],
                ],
                'A15:C15' => [ 
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'EEECE1',
                            ]    
                        ], 
                ],
                'A19:U19' => [ 
                    'font' => [
                        'bold' => true,
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