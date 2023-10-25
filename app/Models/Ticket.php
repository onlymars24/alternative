<?php

namespace App\Models;

// use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'ticketCode',
        'ticketNum',
        'ticketSeries',
        'ticketClass',
        'ticketTypeCode',
        'ticketType',
        'raceUid',
        'raceNum',
        'raceName',
        'raceClassId',
        'dispatchDate',
        'dispatchStation',
        'dispatchAddress',
        'arrivalDate',
        'arrivalStation',
        'arrivalAddress',
        'seat',
        'platform',
        'lastName',
        'firstName',
        'middleName',
        'docTypeCode',
        'docType',
        'docSeries',
        'docNum',
        'citizenship',
        'gender',
        'birthday',
        'phone',
        'email',
        'supplierCurrencyCode',
        'supplierFare',
        'supplierDues',
        'supplierPrice',
        'supplierRepayment',
        'currencyCode',
        'dues',
        'price',
        'vat',
        'repayment',
        'busInfo',
        'carrier',
        'carrierInn',
        'carrierPhone',
        'barcode',
        'status',
        'returned',
        'benefit',
        'hash',
        'order_id',
        'orderBundle',
        'customerItem',
        'duePercent',
        'duePrice'
    ];
    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }
}
