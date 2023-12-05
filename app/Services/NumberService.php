<?php

namespace App\Services;


class NumberService
{

    /**
     * @param string $price
     * @param string $code
     *
     * @return string
     */
    public static function priceToText(string $price, string $code = 'RUB'){
        // Массив видов валют
        $currency = [
            'RUB' => [
                'int' => [
                    'titles'   => ['рубль', 'рубля', 'рублей'],
                    'feminine' => false,
                ],
                'dec' => [
                    'titles'   => ['копейка', 'копейки', 'копеек'],
                    'feminine' => true,
                ],
            ],
            'USD' => [
                'int' => [
                    'titles'   => ['доллар', 'доллара', 'долларов'],
                    'feminine' => false,
                ],
                'dec' => [
                    'titles'   => ['цент', 'цента', 'центов'],
                    'feminine' => false,
                ],
            ],
            'EUR' => [
                'int' => [
                    'titles'   => ['евро', 'евро', 'евро'],
                    'feminine' => false,
                ],
                'dec' => [
                    'titles'   => ['цент', 'цента', 'центов'],
                    'feminine' => false,
                ],
            ],
            'UAH' => [
                'int' => [
                    'titles'   => ['гривна', 'гривны', 'гривен'],
                    'feminine' => true,
                ],
                'dec' => [
                    'titles'   => ['копейка', 'копейки', 'копеек'],
                    'feminine' => true,
                ],
            ],
        ];

        // Если не существует переданной валюты,
        // то устанавливаем значение по-умолчанию
        if ( ! array_key_exists($code, $currency)) {
            $code = 'RUB';
        }

        preg_replace("/[.,]+/", '.', strval($price));

        // Делим на рубли и копейки
        $segments = explode(".", strval($price));

        // Получаем строку в зависимости от выбранной валюты
        $int = self::numberToText($segments[0],
                $currency[$code]['int']['feminine']) . ' ' .
               self::declOfNumber($segments[0],
                   $currency[$code]['int']['titles']);

        // Если есть копейки
        if (count($segments) > 1) {
            if (strlen($segments[1]) === 1) {
                $segments[1] .= '0';
            }

            $dec = self::numberToText($segments[1],
                    $currency[$code]['dec']['feminine']) . ' ' .
                   self::declOfNumber($segments[1],
                       $currency[$code]['dec']['titles']);

            return "$int $dec";
        }

        return $int;
    }

    /**
     * @param string $num
     * @param bool   $feminine
     *
     * @return string
     */
    public static function numberToText(string $num, $feminine = false)
    {
        // Разряды
        $numbers = [
            0 => [
                '0' => 'ноль',
                '1' => ['один', 'одна'],
                '2' => ['два', 'две'],
                '3' => 'три',
                '4' => 'четыре',
                '5' => 'пять',
                '6' => 'шесть',
                '7' => 'семь',
                '8' => 'восемь',
                '9' => 'девять',
            ],
            1 => [
                '1' => 'десять',
                '2' => 'двадцать',
                '3' => 'тридцать',
                '4' => 'сорок',
                '5' => 'пятьдесят',
                '6' => 'шестьдесят',
                '7' => 'семьдесят',
                '8' => 'восемьдесят',
                '9' => 'девяносто',
            ],
            2 => [
                '1' => 'сто',
                '2' => 'двести',
                '3' => 'триста',
                '4' => 'четыреста',
                '5' => 'пятьсот',
                '6' => 'шестьсот',
                '7' => 'семьсот',
                '8' => 'восемьсот',
                '9' => 'девятьсот',
            ],
        ];

        // Спецчисла
        $teens = [
            '1' => 'одиннадцать',
            '2' => 'двенадцать',
            '3' => 'тринадцать',
            '4' => 'четырнадцать',
            '5' => 'пятнадцать',
            '6' => 'шестнадцать',
            '7' => 'семнадцать',
            '8' => 'восемнадцать',
            '9' => 'девятнадцать',
        ];

        // Системы чисел
        $numberSystems = [
            1 => ['тысяча', 'тысячи', 'тысяч'],
            2 => ['миллион', 'миллиона', 'миллионов'],
            3 => ['миллиард', 'миллиарда', 'миллиардов'],
        ];

        // Если число равно ноль, возвращаем "ноль"
        if (intval($num) === 0) {
            return $numbers[0]['0'];
        }

        $resultArray = [];

        // Делим число на сегменты по три знака с конца
        $numItems = preg_split("/(?=(?:\d{3})+(?!\d))/", strval($num));

        // Перебираем сегменты с конца
        foreach (array_reverse($numItems) as $itemId => $item) {
            $result = '';

            // Делим сегмент на цифры и переворачиваем
            $itemArray = array_reverse(preg_split('//u', $item,
                null, PREG_SPLIT_NO_EMPTY));

            // Перебираем цифры
            foreach ($itemArray as $elId => $el) {
                // Если первая цифра тысяч равна 1 или 2, но тысячи не заканчиваются на 11 и 12,
                // , то добавляем в результат число женского рода и пропускаем итерацию
                if ($itemId === 1 && $elId === 0
                    && (($item % 10 === 1 && $item % 100 !== 11)
                        || ($el === '2' && $item % 100 !== 12))
                ) {
                    $result = $numbers[$elId][$el][1] . ' ' . $result;

                    continue;
                }

                // Если цифр в сегменте больше одной
                if (count($itemArray) > 1) {
                    // Если находимся на первой цифре и вторая цифра равна 1
                    // , или если текущая цифра равна 0, то пропускаем итерацию
                    if (($elId === 0 && $itemArray[1] === '1') || $el === '0') {
                        continue;
                    }

                    // Иначе если находимся на второй цифре и она равна 1
                    // , и первая цифра не равна 0, то добавляем спецчисло в результат
                    // и пропускаем итерацию
                    elseif ($elId === 1 && $itemArray[0] !== '0'
                            && $el === '1'
                    ) {
                        $result = $teens[$itemArray[0]] . ' ' . $result;

                        continue;
                    }
                }

                // Если первая цифра равна 1 или 2, то добавляем в результат
                // число выбранного рода и пропускаем итерацию
                if ($elId === 0 && ($el === '1' || $el === '2')) {
                    $result = $numbers[$elId][$el][$feminine ? 1 : 0]
                              . ' ' . $result;

                    continue;
                }

                // Добавляем цифру исходя из разряда
                $result = $numbers[$elId][$el] . ' ' . $result;
            }

            // Если результат не пустой
            if ($result = trim($result, ' .,\t\n\r')) {
                // Если сегмент не первый, то добавляем систему чисел в результат
                if ($itemId) {
                    $system = self::declOfNumber($item,
                        $numberSystems[$itemId]);

                    $result = $result . ' ' . $system;
                }

                // Добавляем результат сегмента в массив
                array_unshift($resultArray, $result);
            }
        }

        // Выводим массив результатов через разделитель
        return implode(' ', $resultArray);
    }

    // Склонение числительных
    public static function declOfNumber($n, array $titles)
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $titles[($n % 100 > 4 && $n % 100 < 20)
            ? 2
            : $cases[($n % 10 < 5) ? $n % 10 : 5]];
    }
}