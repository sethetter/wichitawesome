@extends('app')

@section('title', 'Wichitawesome!')
@section('description', 'Find upcoming events in Wichita, KS!')

@section('container', 'container')

@section('content')
    <div id="event_list">
        @include('events.partials.list')
    </div>

    @if($events->hasMorePages())
        <div class="center">
            <a id="pagination_next" class="btn" href="{{ $events->nextPageUrl() }}">More Events!</a>
        </div>
    @endif
@endsection