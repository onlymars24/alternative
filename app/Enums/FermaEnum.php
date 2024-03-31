<?php

namespace App\Enums;

class FermaEnum
{    
    public static $body =  [
        "Request" => [
            "Inn" => "7017479680", 
            "Type" => null, 
            "InvoiceId" => null, 
            "CustomerReceipt" => [
                "TaxationSystem" => 2, 
                "Email" => "info@rosvokzaly.ru",
                "PaymentType" => 1, 
                "BillAddress" => "https://росвокзалы.рф/", 
                "Items" => [], 
                "PaymentItems" => [
                [
                    "PaymentType" => 1,
                    "Sum" => null
                ]
            ]
            ]
        ] 
    ];

    public static $item = [
        "Label" => null, 
        "Price" => null, 
        "Quantity" => 1, 
        "Amount" => null, 
        "Vat" => "VatNo", 
        "PaymentMethod" => 4, 
        "PaymentType" => 4, 
        // "AdditionalRequisite" => "Какая-то строка для скидки",
        "PaymentAgentInfo" => [
           "AgentType" => "AGENT", 
           "SupplierInn" => "2221122730", 
           "SupplierName" => "\"Артмарк\" ООО", 
           "SupplierPhone" => "73852359311" 
        ] 
    ];

    public static $insurance = [
        "Label" => 'Страховка', 
        "Price" => null, 
        "Quantity" => 1, 
        "Amount" => null, 
        "Vat" => "VatNo", 
        "PaymentMethod" => 4, 
        "PaymentType" => 4, 
        "PaymentAgentInfo" => [
           "AgentType" => "AGENT", 
           "SupplierInn" => "7713056834", 
           "SupplierName" => "АО «АльфаСтрахование»",
           "SupplierPhone" => "88003330999"
        ]
    ];

    public static $percent = [
        "Label" => "Сервисный сбор",
        "Price" => null,
        "Quantity" => 1,
        "Amount" => null,
        "Vat" => "VatNo", 
        "PaymentMethod" => 4, 
        "PaymentType" => 4 
    ];

    public static $bonuses = [
        "Label" => "Скидка", 
        "Price" => null, 
        "Quantity" => 1, 
        "Amount" => null, 
        "Vat" => "VatNo", 
        "PaymentMethod" => 4, 
        "PaymentType" => 4, 
        "PaymentAgentInfo" => [
           "AgentType" => "AGENT", 
           "SupplierInn" => "2221122730", 
           "SupplierName" => "\"Артмарк\" ООО", 
           "SupplierPhone" => "73852359311" 
        ] 
    ];


}