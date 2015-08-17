<div class="venue py2">
    <div class="venue-head">
        <h2 class="caps m0"><a href="{{ action('VenueController@show', $venue->id) }}">{{ $venue->name or $venue->street }}</a></h2>
        <div>
            <span class="icon icon-location">Location: </span>
            {{ $venue->address() }}
        </div>
       {{--  <div class="venue-actions">
            <ul>
                <li><span class="icon icon-share">Share</span>
                    <ul>
                        <li><a href="{{ action('VenueController@show', $venue->id) }}"><span class="icon icon-link"></span> Link</a></li>
                    </ul>
                </li>
                @if(Auth::id() === $venue->user_id)
                    <li><a class="icon icon-edit" href="{{ action('venueController@edit', $venue->id) }}">Edit</a></li>
                @endif
            </ul>
        </div> --}}
    </div>
    <div class="venue-body">
        <div><span class="icon icon-description"></span>{!! nl2br(e($venue->description)) !!}</div>
        <div><span class="icon icon-facebook"></span><a target="_blank" href="https://facebook.com/{{ $venue->facebook }}">Facebook</a></div>
    </div>
</div>