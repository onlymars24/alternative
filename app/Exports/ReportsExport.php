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
        $tickets[] = ['Удержание', '', $this->request->holds, $this->request->holdsSupplierDues, $this->request->holdsDues, $this->request->holdsTotal, $this->request->holdsSiteCommission ];
        $tickets[] = ['Сумма для E-traffic', '', '', '', '', $this->request->eTrafficTotal, ''];    
        $tickets[] = [''];        
        $tickets[] = [
'Дата и время отправления
(местное)', 'Дата и время брони
(GMT +3)', 'Дата и время возврата
(местное)', 'Часовой 
пояс', 'Номер 
билета', 'ID заказа', 'Пункт 
отправления', 'Пункт прибытия', 'Фамилия', 'Имя', 'Отчество', 'Статус рейса', 'Статус 
билета', 'Стоимость', 'Сбор 
поставщика', 'Сбор 
агента', 'Удержание', 'Комиссия 
сайта'];
        foreach($this->tickets as $ticket){
            $tickets[] = [$ticket->dispatchDate, $ticket->created_at, $ticket->returned, $ticket->timezone, $ticket->ticketNum, $ticket->order_id, $ticket->dispatchStation, $ticket->arrivalStation, $ticket->lastName, $ticket->firstName, $ticket->middleName, $ticket->raceCancelled ? 'Отменён' : 'Не отменён', $ticketStatuses[$ticket->status]['label'], $ticket->price, $ticket->supplierDues, $ticket->dues, $ticket->status == 'R' ? (string)($ticket->price - $ticket->repayment) : '0', $ticket->duePrice ];
        }

        return 
            $tickets
        ;
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
                'A6:G9' => [    'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],],
                'A6:A9' => [ 'font' => 
                [
                    'bold' => true,
                ],
                ],
                'B5:H5' => [ 'font' => 
                [
                    'bold' => true,
                ],
                ],
                'A11:R11' => [ 
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
