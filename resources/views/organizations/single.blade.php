<div class="orgnization py2">
    <div class="orgnization-head">
        <h2 class="caps m0"><a href="{{ action('OrganizationController@show', $orgnization->id) }}">{{ $orgnization->name }}</a></h2>
    </div>
    <div class="orgnization-body">
        <div><span class="icon icon-description"></span>{!! nl2br(e($orgnization->description)) !!}</div>
        <div><span class="icon icon-facebook"></span><a target="_blank" href="https://facebook.com/{{ $orgnization->facebook }}">Facebook</a></div>
    </div>
</div>