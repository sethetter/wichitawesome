<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title') | Wichitawesome!</title>
    <meta name="description" content="@yield('description')">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link href='http://fonts.googleapis.com/css?family=Domine:400,700|Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @if(Session::has('message'))
        <div class="p2 center bg-green white font-heading">{!! Session::get('message') !!}</div>
    @endif
    <div class="head bg-light-blue clearfix center rel z1">
        <div class="abs b0 col col-12 center" style="font-size:0;">
            <a class="logo inl-blk" href="{{ url('/') }}">
                <object type="image/svg+xml" class="blk" data="{{ asset('img/wichitawesome.svg') }}">
                    Wichitawesome!
                </object>
            </a>
        </div>
        <nav class="admin-nav inl-blk rel z2">
            <ul class="list0 m0">
                @if (Auth::user())
                    <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('events/admin*') ) ? 'red' : '' }}" href="{{ url('events/admin') }}">Events</a></li>
                    <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('venues/admin*') ) ? 'red' : '' }}" href="{{ url('venues/admin') }}">Venues</a></li>
                    <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('users/admin*') ) ? 'red' : '' }}" href="{{ url('users/admin') }}">Users</a></li>
                    <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('roles/admin*') ) ? 'red' : '' }}" href="{{ url('roles/admin') }}">Roles</a></li>
                    <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('permissions/admin*') ) ? 'red' : '' }}" href="{{ url('permissions/admin') }}">Permissions</a></li>
                    <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('auth/logout') ) ? 'red' : '' }}" href="{{ url('auth/logout') }}">Logout</a></li>
                @endif
                <li class="inl-blk"><a class="blx px1 light h6 white caps {{ ( Request::is('events/submit') ) ? 'red' : '' }}" href="{{ url('events/submit') }}">Submit Event</a></li>
                <li class="inl-blk"><a class="blx px1 light h6 white caps {{ ( Request::is('feedback') ) ? 'red' : '' }}" href="{{ url('feedback') }}">Feedback</a></li>
            </ul>
        </nav>
    </div>
    <div class="px2 py4 @yield('container')">
        @yield('content')
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="{{ asset('js/vendor/jquery-2.1.4.min.js') }}"><\/script>')</script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>