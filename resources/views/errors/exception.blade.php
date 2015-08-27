<!DOCTYPE html>
<html style="height:100%;">
    <head>
        <title>Server Error.</title>
        <link href='https://fonts.googleapis.com/css?family=Domine:400,700|Montserrat:400,700' rel='stylesheet' type='text/css'>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body class="tbl bg-light-blue center rel" style="height:100%;">
        <div class="tbl-cell center abs b0 col-12" style="font-size:0;">
            <a class="inl-blk" style="width:80%;max-width:406px;" href="{{ url('/') }}">
                <object type="image/svg+xml" class="blk" data="{{ asset('img/wichitawesome.svg') }}">
                    Wichitawesome!
                </object>
            </a>
        </div>
        <div class="tbl-cell font-heading caps h1 red p3" style="text-shadow: 1px 1px #4c121d;">You broke something...</div>
    </body>
</html>