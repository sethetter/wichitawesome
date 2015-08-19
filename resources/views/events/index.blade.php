@extends('app')

@section('title', 'Events')
@section('description', 'Upcoming events in Wichita, KS')

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

@section('scripts')
    <script>
        scrollawesome();
        $(function() {
            scrollFrame('.event-name');
        });
    </script>
@endsection