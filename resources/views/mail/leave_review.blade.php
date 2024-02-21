<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Document</title>
 
</head>
<body>
    <!-- <div class="header__logo" style="margin-top: 15px;">
        <a href="https://росвокзалы.рф" style="align-items: center; color: black;
            text-decoration: none;
            display: flex;
            align-items: flex-end;"> 
            <img src="https://xn--80adplhnbnk0i.xn--p1ai/assets/741411-b74a89de.png" alt="" class="header-inner-image" style="margin-right: 5px; width: 25px;">
            <span style="font-size: 24px;" class="">Росвокзалы.рф</span>
        </a>
    </div>     -->
    <p>Расскажите, как прошла ваша поездка {{$ticket->raceName}} {{$ticket->dispatchDate}}</p>
    <div class=""><a href="https://vk.me/rosvokzaly">Оставить отзыв</a></div>
    <br><br>
    <p>--</p>
    <p>С уважением,
поддержка <a href="https://росвокзалы.рф">Росвокзалы.РФ</a> </p>
</body>
</html>