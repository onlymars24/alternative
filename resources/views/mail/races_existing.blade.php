<p>{{ $status }} попытка найти рейс для точек {{ $points }}.</p>

<p>Id заказа: {{$orderId}}</p>
<p>Телефон пользователя: {{$phone}}</p>
<p>Написать в WhatsApp: <a href="{{$whatsLink}}">{{$whatsLink}}</a></p>
@if($from_admin)
<p><strong>Проверка от админа</strong></p>
@endif