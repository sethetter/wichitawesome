@if($errors->has())
    <ul class="list0 m0 p1 mb2 h5 bg-dark-red font-heading">
        @foreach ($errors->toArray() as $name => $message)
            <li><a class="white" href="#{{ $name }}">{{ $message[0] }}</a></li>
        @endforeach
    </ul>
@endif