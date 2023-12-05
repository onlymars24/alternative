<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NumberService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function export(Request $request){
        // dd(NumberService::priceToText(473329.94));
        // $txt = "привет букет";
        // $str = mb_strtoupper(substr($txt,0,2));
        // $txt[0] = $str[0];
        // $txt[1] = $str[1];
        // dd($txt); // Привет букет
        $reward = ceil($request->eTrafficTotal / 100)/100;
        $result = $request->eTrafficTotal - $reward;
        $resultStr = NumberService::priceToText($result);
        $str = mb_strtoupper(substr($resultStr,0,2));
        $resultStr[0] = $str[0];
        $resultStr[1] = $str[1];

        $period = 'c '.date('d.m.Y', strtotime($request->comparingDate1)).'г. по '.date('d.m.Y', strtotime($request->comparingDate2)).'г.';
        // dd($period);
        $months = array( 1 => 'января' , 'февраля' , 'марта' , 'апреля' , 'мая' , 'июня' , 'июля' , 'августа' , 'сентября' , 'октября' , 'ноября' , 'декабря' );

        $lastDay = '"'.(integer)(date('d', strtotime($request->comparingDate2))).'" '.$months[(integer)(date('m', strtotime($request->comparingDate2)))].' '.date('Y', strtotime($request->comparingDate2)).' г.';
        // dd($lastDay);

        $data = [
            'period' => $period,
            'lastDay' => $lastDay,
            'salesTotal' => $request->salesTotal,
            'repayments' => $request->repayments,
            'holdsTotal' => $request->holdsTotal,
            'eTrafficTotal' => $request->eTrafficTotal,
            'reward' => $reward,
            'result' => $result,
            'resultStr' => $resultStr,
        ];
        // dd($data);
        $pdf = Pdf::loadView('pdf_file', $data);
        return $pdf->download('Отчёт ООО Альтернатива - '.$period.'.pdf');
    }
}