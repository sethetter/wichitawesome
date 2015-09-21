@extends('app')

@section('title', 'Wichitawesome!')
@section('description', 'Find upcoming events in Wichita, KS!')

@section('content')
    <div class="clearfix mt3 rel bb b-dark-red">
            <div class="{{ ! Request::get('tags') ? 'js-hide' : 'js-show js-open' }} js-filter-container">
                <div class="container px2">
                    <div class="bg-white mb3">
                        @foreach($tags as $tag)
                            <input type="checkbox" data-toggle="tag" class="tag-checkbox sr-only" id="tag_{{ $tag->id }}" name="tags[]" value="{{ $tag->slug }}" {{ in_array($tag->slug, explode(',', Request::get('tags'))) ? 'checked' : '' }}>
                            <label for="tag_{{ $tag->id }}" class="inl-blk tag b bg-white">{{ $tag->name }}</label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="abs center col-12" style="bottom:-1.2rem;"><a href="#" class="inl-blk font-heading py1 px2 bg-white light-blue caps h6 track js-filter-btn">{{ ! Request::get('tags') ? 'Filter' : 'Close' }}</a></div>
    </div>
    <div class="px2 py3 mb2 container clearfix">
        <div id="event_list">
            @include('events.partials.list')
        </div>

        @if($events->hasMorePages())
            <div class="center">
                <a id="pagination_next" class="btn" href="{{ $events->nextPageUrl() }}">More Events!</a>
            </div>
        @endif
    </div>
@endsection