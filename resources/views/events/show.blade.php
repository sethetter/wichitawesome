@extends('app')

@section('title', $event->name)
@section('description', '')

@section('container', 'container')

@section('content')
    <div class="event mb4">
        <?php $now = date('Yjn'); ?>
        <h2 class="event-date bg-white m0 center regular left {{ $event->start_time->format('Yjn') == $now ? 'red' : '' }}">
            <div class="caps h6">{{ $event->start_time->format('M') }}</div>
            <div>{{ $event->start_time->format('d') }}</div> 
        </h2>
        <div class="event-content ml3 px2 o-hidden">
            <h3 class="mt0 mb1 regular dark-red">{{ $event->name }}</h3>
            <div id="map" class="col-12 mb1" style="height:200px;"></div>
            <div class="inl-blk mr1">
                <span class="i i-location dark-red"></span> 
                @if(isset($event->venue->name))
                    <a class="event-venue" target="_blank" href="https://www.google.com/maps/dir/Current+Location/{{ $event->venue->latitude }},{{ $event->venue->longitude }}" title="{{ $event->venue->address() }}">{{ $event->venue->name }}</a>
                @else
                    {{ $event->venue->street }}
                @endif
            </div>
            <div class="inl-blk">
                <span class="i i-clock dark-red"></span> {{ $event->displayTime() }}
            </div>
            <div><span class="i i-facebook dark-red"></span> <a target="_blank" href="https://facebook.com/events/{{ $event->facebook }}">Facebook Event</a></div>
            <div class="event-details rel">
                <span class="i i-description dark-red abs l0"></span>
                {!! $event->displayDesc() !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function(){
            maps.loadApi().done(function(){
                var latLng = new google.maps.LatLng({{$event->venue->latitude}}, {{$event->venue->longitude}});
                maps.setMap('map', {center: latLng}).setMarker(latLng);
            });
        });
    </script>
@endsection
