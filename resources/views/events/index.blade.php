@extends('app')

@section('title', 'Events')
@section('description', '')

@section('container', 'container')

@section('content')
    <div id="event_list">
        @if( count($events) )
            <?php $dateHeading = false; ?>
            <?php $now = date('Yjn'); ?>
            @foreach($events as $event)
                <?php $date = $event->start_time->format('Yjn'); ?>
                <div class="event mb4">
                    @if($dateHeading != $date)
                        <h2 class="event-date bg-white m0 center regular left {{ $date == $now ? 'red' : '' }}">
                            <div class="caps h6">{{ $event->start_time->format('M') }}</div>
                            <div>{{ $event->start_time->format('d') }}</div> 
                        </h2>
                    @endif
                    <div class="event-content ml3 px2 o-hidden">
                        <h3 class="mt0 mb1 regular"><a class="event-name dark-red" href="{{ action('EventController@show', $event->id) }}">{{ $event->name }}</a></h3>
                        <div class="inl-blk mr1">
                            <span class="i i-location"></span> 
                            @if(isset($event->venue->name))
                                <a class="event-venue" target="_blank" href="https://www.google.com/maps/dir/Current+Location/{{ $event->venue->latitude }},{{ $event->venue->longitude }}" title="{{ $event->venue->address() }}">{{ $event->venue->name }}</a>
                            @else
                                {{ $event->venue->street }}
                            @endif
                        </div>
                        <div class="inl-blk">
                            <span class="i i-clock black"></span> {{ $event->displayTime() }}
                        </div>
                        <div><span class="i i-facebook"></span> <a target="_blank" href="https://facebook.com/events/{{ $event->facebook }}">Facebook Event</a></div>
                    </div>
                </div>
                <?php $dateHeading = $date; ?>
            @endforeach
        @else
            <div class="p1 mb2 font-heading white dark-red">
                <strong>Well, there's no more events.</strong> Go read a book.
            </div>
        @endif
    </div>

    @if($events->hasMorePages())
        <div class="center">
            <a id="pagination_next" class="btn" href="{{ $events->nextPageUrl() }}">More Events!</a>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        scrollawesome();
        $(function() {
            scrollFrame('.event-name');
        });
    </script>
@endsection