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
    <div class="header__logo" style="margin-top: 15px;">
        <a href="https://росвокзалы.рф" style="align-items: center; color: black;
            text-decoration: none;
            display: flex;
            align-items: flex-end;"> 
            <img src="{{$message->embed('img/741411.png')}}" alt="" class="header-inner-image" style="margin-right: 5px; width: 25px;"> 
            <span style="font-size: 24px;" class="">Росвокзалы.рф</span>
        </a>
    </div>    
    <p>Вы успешно оформили заказ {{$tickets[0]->order_id}}: {{$tickets[0]->raceName}} {{$tickets[0]->dispatchDate}}</p>
    <ul>
        @foreach($tickets as $ticket)
            <li>Билет №{{$ticket->ticketNum}} {{$ticket->lastName}} {{$ticket->firstName}} {{$ticket->middleName}} Место {{$ticket->seat}}</li>
        @endforeach
    </ul>

    <h2>
        Как вернуть часть стоимости билетов
    </h2>
    <p>
        <span class="text-small"><strong>Всё просто — поделитесь ВКонтакте ссылкой на наш сайт:</strong></span>
    </p>
    <p>
        <span class="text-small">𝟏. Нажмите ниже на кнопку "Поделиться".</span>
    </p>
    <p>
        <span class="text-small">𝟐. На вашей странице ВК допишите комментарий и опубликуйте его.</span>
    </p>
    <p>
        <span class="text-small">𝟑. Подпишитесь на </span><a href="https://vk.com/rosvokzaly" target="_blank"><span class="text-small">официальную страницу Автовокзалы России</span></a><span class="text-small">. Так мы увидим, что вы поделились ссылкой, и зачислим часть стоимости поездки на ваш баланс на сайте.</span>
    </p>
    <a style="display: block;" href="https://vk.com/share.php?url=https://росвокзалы.рф" target="_blank">
        <button style="padding: 9px 12px; display: flex; align-items: center; border-radius: 5px; color: #fff; background-color: #4C75A3; border: none; cursor: pointer;">
            <img style="width: 27px; margin-right: 7px;" src="{{$message->embed('img/vk_logo_new.png')}}" alt="">
            <span style="font-size: 18px;">Поделиться</span>
        </button>
    </a>

    <p>
        Будем рады, если <a href="https://vk.com/rosvokzaly">станете нашим подписчиком в ВК</a>:<br>
        - так мы станем ближе и сможем оперативнее отвечать на ваши вопросы,<br>
        - сможете участвовать в акциях и получать различные бонусы,<br>
    </p>
    <div style="border-radius: 15px;">
        <a href="https://vk.com/rosvokzaly" target="_blank">
            <img src="{{$message->embed('img/vk_bus_mail.png')}}" alt="">
        </a>
    </div>
    <br><br>
    <p>--</p>
    <p>С уважением,
поддержка <a href="https://росвокзалы.рф">Росвокзалы.РФ</a> </p>
</body>
</html>