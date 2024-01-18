@if($correct)
<p>{{$action}} для <strong>{{ $phone }}</strong> прошла успешно!</p>
@else
<p>{{$action}} для <strong>{{ $phone }}</strong> не прошла! Код неверный!</p>
@endif