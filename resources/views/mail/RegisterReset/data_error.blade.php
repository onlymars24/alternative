<p>Данные для {{$action}} по номеру {{ $phone }} имеют следующие ошибки:</p>
<ul>
    @foreach($errors as $key => $error)
    @foreach($error as $el)
        <li>{{$el}}</li>
    @endforeach
    @endforeach
</ul>