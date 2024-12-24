<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GraphicService
{
    public static function generateImage($dispatchPointName, $arrivalPointName){
        $imagePath = public_path('img/bus_bgc.jpg');
        $image = imagecreatefromjpeg($imagePath);

        $text_color = imagecolorallocate($image, 255, 255, 255); 

        // Function to create image which contains string. 

        $width = imagesx($image);
        $height = imagesy($image);
        // Get center coordinates of image
        $centerX = $width / 2;
        $centerY = $height / 2;
        // Get size of text
        $dispatchPointName = strlen($dispatchPointName) <= 16 ? $dispatchPointName : mb_substr($dispatchPointName, 0, 16);
        $arrivalPointName = strlen($arrivalPointName) <= 16 ? $arrivalPointName : mb_substr($arrivalPointName, 0, 16);
        // dd($dispatchPointName, $arrivalPointName);
        $text = $dispatchPointName.' — 
'.$arrivalPointName;
        $font = public_path('fonts/FiraSansCondensed-Medium.ttf');
        $size = 65;
        $maxWidth = 740;
      
        list($left, $bottom, $right, , , $top) = imageftbbox($size, 0, $font, $text);
        // Determine offset of text
        $left_offset = ($right - $left) / 2;
        $top_offset = ($bottom - $top) / 2;
        // Generate coordinates
        $x = $centerX - $left_offset;
        $y = 400;


        // $x = 100;
        // $y = 200;
      
      
        imagefttext(
            $image,
            $size,
            0,
            $x+3,
            $y+3,
            imagecolorallocate($image, 59, 58, 58),
            $font,
            $text,
            []
        );

        imagefttext(
          $image,
          $size,
          0,
          $x,
          $y,
          $text_color,
          $font,
          $text,
          []
        );
      
      
        imagejpeg($image, public_path('routes/new_bus_bgc.jpg'));
    }

    public static function wrapText($image, $text, $fontPath, $fontSize, $startX, $startY, $maxWidth) {
        $words = explode(' ', $text);
        $wrappedText = '';
        $line = '';
    
        foreach ($words as $word) {
            // Проверяем, не превышает ли ширина строки максимально допустимую
            $testLine = $line . ' ' . $word;
            $testBox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
            $testWidth = $testBox[2] - $testBox[0];
    
            if ($testWidth > $maxWidth) {
                // Если строка слишком длинная, начинаем новую строку
                $wrappedText .= $line . PHP_EOL;
                $line = $word;
            } else {
                $line = $testLine;
            }
        }
        $wrappedText .= $line;
    
        // Разбиваем текст на строки
        $lines = explode(PHP_EOL, $wrappedText);
        $textColor = imagecolorallocate($image, 255, 255, 255); 
        // Выводим текст на изображение
        foreach ($lines as $i => $line) {
            // imagefttext($image, $fontSize, 0, $startX, $startY + ($i * $fontSize * 1.5), $textColor, $fontPath, $line);

            imagefttext(
                $image,
                $fontSize,
                0,
                $startX, 
                $startY + ($i * $fontSize * 1.5),
                $textColor, 
                $fontPath, 
                $line,
                []
            );

        }
        return $image;
    }
}