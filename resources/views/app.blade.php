<!doctype html>
<html itemscope itemtype="http://schema.org/WebPage" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Wichitawesome!')</title>
    <meta name="description" content="@yield('description')">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="@yield('title')">
    <meta itemprop="description" content="@yield('description')">
    <meta itemprop="image" content="{{ asset('img/wichitawesome.png') }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@yeah_wichita">
    <meta name="twitter:title" content="@yield('title')">
    <meta name="twitter:description" content="@yield('description')">
    <meta name="twitter:creator" content="@ima_crayon">
    <!-- Twitter summary card with large image must be at least 280x150px -->
    <meta name="twitter:image:src" content="{{ asset('img/wichitawesome.png') }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:image" content="{{ asset('img/wichitawesome.png') }}" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:site_name" content="Wichitawesome!" />
    <meta property="fb:app_id" content="1450071418617846" />

    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link href='//fonts.googleapis.com/css?family=Domine:400,700|Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @include('icons')
    <div class="main">
        <div class="main-content">
            @if(Session::has('message'))
                <div class="p2 center bg-green white font-heading">{!! Session::get('message') !!}</div>
            @else
                <div class="p1 center bg-dark-red light-blue font-heading h6"><small><strong>We're in beta!</strong> <a class="light-blue underline" href="{{ url('feedback') }}">Let us know if you find anything weird.</a></small></div>
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
                            <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('organizations/admin*') ) ? 'red' : '' }}" href="{{ url('organizations/admin') }}">Organizations</a></li>
                            <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('tags/admin*') ) ? 'red' : '' }}" href="{{ url('tags/admin') }}">Tags</a></li>
                            <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('users/admin*') ) ? 'red' : '' }}" href="{{ url('users/admin') }}">Users</a></li>
                            <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('roles/admin*') ) ? 'red' : '' }}" href="{{ url('roles/admin') }}">Roles</a></li>
                            <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('permissions/admin*') ) ? 'red' : '' }}" href="{{ url('permissions/admin') }}">Permissions</a></li>
                            <li class="inl-blk"><a class="blk px1 light h6 white caps {{ ( Request::is('auth/logout') ) ? 'red' : '' }}" href="{{ url('auth/logout') }}">Logout</a></li>
                        @endif
                        <li class="inl-blk"><a class="blx px1 light h6 white caps {{ ( Request::is('events/submit') ) ? 'red' : '' }}" href="{{ url('events/submit') }}">Add Event</a></li>
                        <li class="inl-blk"><a class="blx px1 light h6 white caps {{ ( Request::is('feedback') ) ? 'red' : '' }}" href="{{ url('feedback') }}">Feedback</a></li>
                        <li class="inl-blk"><a class="blx px1 light h6 white caps" href="//blog.wichitaweso.me">Blog</a></li>
                    </ul>
                </nav>
            </div>
            @yield('content')
        </div>
    </div>
    <div class="foot p2 bg-light-blue dark-red center font-heading h5">
        <small>Wichitawesome! Copyright &copy; Christian Taylor.</small>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-66355445-1', 'auto');
        ga('send', 'pageview');
    </script>
</body>
</html>