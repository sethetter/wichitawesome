@if( count($events) )
    <?php $now = date('Yjn'); ?>
    @foreach($events as $event)
        <div itemscope itemtype="http://schema.org/Event" class="event mb4">
            <time datetime="{{ $event->start_time->format('c') }}" class="event-date bold bg-white m0 center regular left {{ $event->start_time->format('Yjn') == $now ? 'red' : '' }}" title="{{ $event->start_time->format('g:i A \o\n D, M d, Y') }}">
                <meta itemprop="startDate" content="{{ $event->start_time->format('c') }}">
                <span class="blk caps h6 font-heading">{{ $event->start_time->format('D') }}</span>
                <span class="blk h2 font-heading">{{ $event->start_time->format('d') }}</span>
                <span class="blk caps h6 font-heading light-gray">{{ $event->start_time->format('M') }}</span>
            </time>
            <div class="event-content ml3 px2 o-hidden h5">
                <h2 class="mt0 mb1 regular h4"><a itemprop="url" class="event-name dark-red" href="{{ action('EventController@show', $event->id) }}">{{ $event->name }}</a></h2>
                <div itemprop="location" itemscope itemtype="http://schema.org/PostalAddress" class="inl-blk mr1">
                    <svg class="i"><use xlink:href="#icon-location"></use></svg>
                    @if(isset($event->venue->name))
                        <a class="event-venue" target="_blank" href="https://www.google.com/maps/dir/Current+Location/{{ $event->venue->latitude }},{{ $event->venue->longitude }}" title="{{ $event->venue->address() }}">{{ $event->venue->name }}</a>
                        <meta itemprop="streetAddress" content="{{ $event->venue->street }}">
                    @else
                        <span itemprop="streetAddress">{{ $event->venue->street }}</span>
                    @endif
                    <meta itemprop="addressLocality" content="{{ $event->venue->city }}">
                    <meta itemprop="addressRegion" content="{{ $event->venue->state }}">
                    <meta itemprop="postalCode" content="{{ $event->venue->zip }}">
                </div>
                <div class="inl-blk">
                    <svg class="i"><use xlink:href="#icon-clock"></use></svg> {{ $event->displayTime() }}
                </div>
                @if($event->facebook)
                    <div><svg class="i"><use xlink:href="#icon-facebook"></use></svg> <a target="_blank" href="https://facebook.com/events/{{ $event->facebook }}">Facebook Event</a></div>
                @endif
                @if($event->tags)
                    <div class="mt1">
                        @foreach($event->tags as $tag)
                            <label for="tag_{{ $tag->id }}" class="inl-blk tag b {{ in_array($tag->slug, explode(',', Request::get('tags'))) ? 'b-light-blue light-blue' : '' }}">{{ $tag->name }}</label>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="p1 mb2 font-heading white dark-red">
        <strong>Well, there's no more events.</strong> Go read a book.
    </div>
@endif