<!DOCTYPE html>
<html lang="ru">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>file_1701687578624</title>
  <style type="text/css">
    * {
      margin: 0;
      padding: 0;
      /* text-indent: 0; */
      font-family: DejaVu Sans !important;
    }
    
    h2 {
      color: black;
      /* font-family: Arial, sans-serif; */
      /* font-style: italic; */
      font-weight: bold;
      text-decoration: none;
      font-size: 10pt;
      margin-bottom: 5px;
    }
    
    .p,
    p {
      color: black;
      /* font-family: Arial, sans-serif; */
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 9pt;
      margin: 0pt;
    }
    
    .s1 {
      color: black;
      /* font-family: Arial, sans-serif; */
      /* font-style: italic; */
      font-weight: bold;
      text-decoration: none;
      font-size: 10pt;
    }
    
    .s2 {
      color: black;
      /* font-family: Arial, sans-serif; */
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 11pt;
    }
    
    h1 {
      color: black;
      /* font-family: Arial, sans-serif; */
      /* font-style: italic; */
      font-weight: bold;
      text-decoration: none;
      font-size: 10pt;
    }
    
    .s3 {
      color: black;
      /* font-family: "Times New Roman", serif; */
      font-style: normal;
      font-weight: normal;
      text-decoration: underline;
      font-size: 8pt;
    }
    
    .s4 {
      color: black;
      /* font-family: "Times New Roman", serif; */
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 8pt;
    }
    
    .s5 {
      color: #0054BE;
      /* font-family: "Segoe UI Light", sans-serif; */
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 7pt;
    }
    
    table,
    tbody {
      vertical-align: top;
      overflow: visible;
    }
    td{
      padding: 2px;
    }
  </style>
</head>

<body style="padding: 30px;">
  <h2 style="margin-bottom:0; padding-top: 3pt;padding-left: 214pt;text-indent: 0pt;text-align: left;">ОТЧЕТ Субагента</h2>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <p style="text-indent: 0pt;text-align: center;">{{$period}}</p>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <p style="width:95%; padding-top: 4pt;text-indent: 0pt;text-align: right;">{{$lastDay}}</p>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <p style="width:95%; padding-left: 8pt;text-indent: 0pt;text-align: left;">СУБАГЕНТ ООО “Альтернатива” ИНН 7017479680 по договору № 1GDS-10/23 от 01.08.2023 года оформил перевозочные документы АГЕНТА ООО &quot;Артмарк&quot; ИНН 2221122730 за отчетный период {{$period}}:</p>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <h2 style="text-indent: 0pt;text-align: center;">ВЫРУЧКА ЗА ПРОДАННЫЕ БИЛЕТЫ</h2>
  <table style="border-collapse:collapse;margin-left:7.75pt; width: 95%;" cellspacing="0">
    <tr style="height:10pt">
      <td style="width:80%;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s1" style="padding-left: 164pt;padding-right: 164pt;text-indent: 0pt;line-height: 8pt;text-align: center;">Наименование</p>
      </td>
      <td style="width:20%;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s1" style="padding-left: 23pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Сумма, руб.</p>
      </td>
    </tr>

    @if($salesPassengerSupplierFares > 0)
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s2" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Выручка от продажи пассажирских билетов</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s2" style="text-indent: 0pt;line-height: 8pt;text-align: right;">{{$salesPassengerSupplierFares}}</p>
      </td>
    </tr>
    @endif

    @if($repaymentsPassenger > 0)
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s2" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Выручка от продажи пассажирских билетов</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s2" style="text-indent: 0pt;line-height: 8pt;text-align: right;">-{{$repaymentsPassenger}}</p>
      </td>
    </tr>
    @endif

    @if($salesLuggageSupplierFares > 0)
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s2" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Выручка от продажи багажных билетов</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s2" style="text-indent: 0pt;line-height: 8pt;text-align: right;">{{$salesLuggageSupplierFares}}</p>
      </td>
    </tr>
    @endif

    @if($repaymentsLuggage > 0)
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s2" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Выручка от продажи багажных билетов</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s2" style="text-indent: 0pt;line-height: 8pt;text-align: right;">-{{$repaymentsLuggage}}</p>
      </td>
    </tr>
    @endif

    @if($salesSupplierDues > 0)
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s2" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Выручка от комиссионного сбора автовокзала при продаже билетов (продажа)</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s2" style="text-indent: 0pt;line-height: 8pt;text-align: right;">{{$salesSupplierDues}}</p>
      </td>
    </tr>
    @endif

    @if($salesDues > 0)
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s2" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Выручка от комиссионного сбора агента при продаже билетов (продажа)</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s2" style="text-indent: 0pt;line-height: 8pt;text-align: right;">{{$salesDues}}</p>
      </td>
    </tr>
    @endif

    @if($holdsTotal > 0)
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s2" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Удержания при оформлении возвратов перевозочных документов</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s2" style="text-indent: 0pt;line-height: 8pt;text-align: right;">{{$holdsTotal}}</p>
      </td>
    </tr>
    @endif

    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s1" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">ИТОГО выручка</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s1" style="padding-right: 1pt;text-indent: 0pt;line-height: 8pt;text-align: right;">{{$eTrafficTotal}}</p>
      </td>
    </tr>
  </table>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <h2 style="text-indent: 0pt;text-align: center;">ВОЗНАГРАЖДЕНИЕ ПО ДОГОВОРУ</h2>
  <table style="border-collapse:collapse;margin-left:7.75pt; width: 95%;" cellspacing="0">
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s1" style="padding-left: 164pt;padding-right: 164pt;text-indent: 0pt;line-height: 8pt;text-align: center;">Наименование</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s1" style="padding-left: 23pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Сумма, руб.</p>
      </td>
    </tr>
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s2" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">Вознаграждение от продажи билетов</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s2" style="text-indent: 0pt;line-height: 8pt;text-align: right;">{{$reward}}</p>
      </td>
    </tr>
    <tr style="height:10pt">
      <td style="width:389pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:2pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:1pt;border-right-color:#333333">
        <p class="s1" style="padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">ИТОГО вознаграждение</p>
      </td>
      <td style="width:95pt;border-top-style:solid;border-top-width:2pt;border-top-color:#333333;border-left-style:solid;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:2pt;border-bottom-color:#333333;border-right-style:solid;border-right-width:2pt;border-right-color:#333333">
        <p class="s1" style="padding-right: 1pt;text-indent: 0pt;line-height: 8pt;text-align: right;">{{$reward}}</p>
      </td>
    </tr>
  </table>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <table style="margin-left:7.75pt; width: 95%;">
    <tr style="height:10pt">
      <td style="width:47%;text-align: left;">
        <p>ИТОГО к расчету за данный период</p>
      </td>
      <td style="width:47%;text-align: right;">
        <p class="s1">{{$result}} руб</p>
      </td>
    </tr>
  </table>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <p style="text-indent: 0pt;text-align: center;">{{$resultStr}}</p>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <p style="padding-left: 8pt;text-indent: 0pt;text-align: left;">Подписи сторон:</p>
  <table style="margin-left:7.75pt; width: 95%;">
    <tr style="height:10pt">
      <td style="width:47%;">
        <h1 style="padding-top: 6pt;text-indent: 0pt;text-align: left;">СУБАГЕНТ</h1>
        <p style="padding-top: 6pt;;text-indent: 0pt;text-align: left;">Общество с ограниченной ответственностью<br> “Альтернатива”</p>
        <p style="padding-top: 3pt;text-indent: 0pt;text-align: left;">ИНН: 7017479680</p>
        <p style="padding-top: 1pt;text-indent: 0pt;text-align: left;">Адрес: 634049, Томская обл, Томск г, Иркутский<br> тракт, дом 27, корпус 2, оф. 17</p>
        <p style="padding-top: 1pt;text-indent: 0pt;text-align: left;">Расчетный счет: 40702810523500000809</p>
        <p style="padding-top: 1pt;text-indent: 0pt;text-align: left;">Банк ФИЛИАЛ "НОВОСИБИРСКИЙ" <br> АО "АЛЬФА-БАНК"</p>
      </td>
      <td style="width:47%;">
        <h1 style="padding-top: 6pt;text-indent: 0pt;text-align: left;">АГЕНТ</h1>
        <p style="padding-top: 6pt;text-indent: 0pt;text-align: left;">Общество с ограниченной ответственностью<br> &quot;Артмарк&quot;</p>
        <p style="padding-top: 3pt;text-indent: 0pt;text-align: left;">ИНН: 2221122730</p>
        <p style="padding-top: 1pt;text-indent: 0pt;text-align: left;">Адрес: 656038, Алтайский край, г Барнаул, ул<br> Путиловская, 20Г</p>
        <p style="padding-top: 1pt;text-indent: 0pt;text-align: left;">Расчетный счет: 40702810320140500405</p>
        <p style="padding-top: 1pt;text-indent: 0pt;text-align: left;">В Филиал &quot;Центральный&quot; Банка ВТБ (ПАО) в г.<br> Москве</p>
      </td>
    </tr>
  </table>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <table style="margin-left:7.75pt; width: 95%;">
    <tr style="height:10pt">
      <td style="width:47%;">
        <p style="text-indent: 0pt;text-align: left;">БИК: 045004774</p>
        <p style="padding-top: 1pt;text-indent: 0pt;text-align: left;">Телефон: +79528911724</p>
      </td>
      <td style="width:47%;">
        <p style="text-indent: 0pt;text-align: left;">БИК: 044525411</p>
        <p style="padding-top: 1pt;text-indent: 0pt;text-align: left;">Телефон: 8 (3852) 35-93-11</p>
      </td>
    </tr>
</table>

  
  
  
  
  
  
  
  <!-- <p style="padding-left: 304pt;text-indent: 0pt;text-align: left;"></p>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p>
  <p style="padding-left: 8pt;text-indent: 0pt;text-align: left;">БИК: БИК: 044525411</p>
  <p style="padding-top: 1pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Телефон: +79528911724 Телефон: 8 (3852) 35-93-11</p>
  <p style="padding-top: 1pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Директор Директор</p>
  <p class="s3" style="padding-top: 1pt;padding-left: 11pt;text-indent: 0pt;text-align: left;"> <span class="s4"> </span><span class="p">И.В. Андрейцев </span>&nbsp;<span class="s4"> </span><span class="p">Устюгов Е.Ю.</span></p>
  <p style="padding-top: 1pt;padding-left: 40pt;text-indent: 0pt;text-align: left;">М.П. М.П.</p>
  <p style="text-indent: 0pt;text-align: left;">
    <br/>
  </p> -->
</body>

</html>