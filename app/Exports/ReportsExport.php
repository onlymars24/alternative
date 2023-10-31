<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReportsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $tickets;
    protected $request;

    public function __construct($tickets, $request)
    {
        $this->tickets = $tickets;
        $this->request = $request;
    }
    public function collection()
    {
        $tickets = [];
        $tickets[] = ['От', $this->request->comparingDate1 ];
        $tickets[] = ['До', $this->request->comparingDate2 ];
        $tickets[] = [''];
        $tickets[] = ['', 'Количество', 'Тариф', 'Сбор автовокзала', 'Сбор агента', 'Итого', 'Комиссия сайта'];
        $tickets[] = ['Продажа билетов', $this->request->salesAmount, $this->request->salesSupplierFares, $this->request->salesSupplierDues, $this->request->salesDues, $this->request->salesTotal, $this->request->salesSiteCommission, ];
        $tickets[] = ['Возврат', $this->request->returnsAmount, '', $this->request->returnsSupplierDues, $this->request->returnsDues, $this->request->repayments, $this->request->returnsSiteCommission ];
        $tickets[] = ['Удержание', '', $this->request->holds, $this->request->holdsSupplierDues, $this->request->holdsDues, $this->request->holdsTotal, $this->request->holdsSiteCommission ];
        $tickets[] = [''];        
        $tickets[] = ['Дата и время отправления(местное)', 'Дата и время брони', 'Номер билета', 'ID заказа', 'Пункт отправления', 'Пункт прибытия', 'Фамилия', 'Имя', 'Отчество', 'Статус', 'Стоимость', 'Сбор поставщика', 'Сбор агента', 'Удержание', 'Комиссия сайта'];
        foreach($this->tickets as $ticket){
            $tickets[] = [$ticket->dispatchDate, $ticket->created_at, $ticket->ticketNum, $ticket->order_id, $ticket->dispatchStation, $ticket->arrivalStation, $ticket->lastName, $ticket->firstName, $ticket->middleName, $ticket->status, $ticket->price, $ticket->supplierDues, $ticket->dues, $ticket->status == 'R' ? ($ticket->price - $ticket->repayment) : '0', $ticket->duePrice ];
        }

        return new Collection(
            $tickets
        );
    }
}
