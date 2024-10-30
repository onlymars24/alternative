<?php

namespace App\Services;

use Exception;
use App\Models\Setting;

class AdPdfService
{
    public static function get(){
        $setting = Setting::where('name', 'adPdf')->first();
        $data = json_decode($setting->data);
        return $data ? $data->adPdf : null;
    }

    public static function mergePdf($filePath){
        $adPdf = self::get();
        if(!$adPdf){
            return false;
        }
        try{
            $pdf = new \Jurosh\PDFMerge\PDFMerger;

            // add as many pdfs as you want
            $pdf->addPDF($filePath, 'all', 'vertical')
            //  ->addPDF('path/to/source/file1.pdf', 'all')
                ->addPDF($adPdf, 'all', 'vertical');

            // call merge, output format `file`
            $pdf->merge('file', $filePath);
            return true;
        }
        catch(Exception $e){
            MailService::sendError('Ошибка слияния PDF', ['ticket' => $filePath]);
            return false;
        }
    }

    public static function testMergePdf($adPdf){     
        try{
            $pdf = new \Jurosh\PDFMerge\PDFMerger;

            $pdf->addPDF('settings/test_ticket.pdf', 'all', 'vertical')
                ->addPDF($adPdf, 'all', 'vertical');

            $pdf->merge('file', 'settings/test_merge.pdf');
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
}