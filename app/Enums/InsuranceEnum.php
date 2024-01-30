<?php

namespace App\Enums;

class InsuranceEnum
{    
    public static $body = [
        "product" => [
              "code" => "ON_BUS_Naavtobus" 
           ], 
        "customer_email" => "customer@email.com", 
        "customer_phone" => "79201111222", 
        "insureds" => [], 
        "segments" => [
            [
                "departure" => [
                    "date" => "2023-12-22T08:00:01", 
                    "point" => "Москва" 
                ], 
                "arrival" => [
                        "date" => "2024-01-12T12:00:00", 
                        "point" => "Санкт-Петербург" 
                    ] 
            ] 
        ]
     ];

     public static $insured = [
        "first_name" => "", 
        "last_name" => "", 
        "patronymic" => "", 
        "birth_date" => "", 
        "gender" => "", 
        "phone" => [
            "number" => "" 
        ], 
        "ticket" => [
            "price" => [
                "value" => 0, 
                "currency" => "RUB" 
            ], 
            "issue_date" => "", 
            "number" => "" 
        ] 
    ]; 


}