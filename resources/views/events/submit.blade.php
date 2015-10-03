@extends('app')

@section('title', 'An Awesome New Event | Wichitawesome!')
@section('description', 'Add an awesome new event to our website!')

@section('content')
    <div class="px2 py4 container clearfix">
        <h2 class="mt0 mb3">Show us whatcha' got.</h2>
        <form method="post" action="{{ action('EventController@collect') }}">
            <div class="form-head">
                @include('errors.form')
            </div>
            <div class="form-body mxn1">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="rel mb2 col col-12 px1">
                    <button type="button" class="btn caps right bg-light-gray" id="btn_facebook">Pull Info</button>
                    <div class="o-hidden">
                        <label for="fb_url"  data-url="fb-event" class="caps h5 abs">Facebook URL</label>
                        <input type="url" class="blk field col-12 rel z1" id="fb_url" name="fb_url" value="{{ old('fb_url', $fb_url) }}" autofocus>
                        <input type="hidden" id="facebook" name="facebook" value="{{ old('facebook') }}">
                    </div>
                </div>

                <div class="rel mb2 col col-12 px1">
                    <label for="name" class="caps h5 abs">Name</label>
                    <input type="text" class="field blk col-12 rel z1" id="name" name="name" value="{{ old('name') }}">
                </div>

                <div class="rel mb2 col col-12 px1">
                    <button type="button" class="btn caps right bg-light-gray" id="btn_map">Map It</button>
                    <div class="o-hidden">
                        <label for="venue_name" class="caps h5 abs">Location</label>
                        <input type="text" class="field col-12 rel z1" id="venue_name" name="venue[name]" value="{{ old('venue.name') }}">
                        <input type="hidden" id="venue_id" name="venue_id" value="{{ old('venue_id') }}">
                        <input type="hidden" id="venue_facebook" name="venue[facebook]" value="{{ old('venue.facebook') }}">
                        <input type="hidden" id="street" name="venue[street]" value="{{ old('venue.street') }}">
                        <input type="hidden" id="city" name="venue[city]" value="{{ old('venue.city') }}">
                        <input type="hidden" id="state" name="venue[state]" value="{{ old('venue.state') }}">
                        <input type="hidden" id="zip" name="venue[zip]" value="{{ old('venue.zip') }}">
                        <input type="hidden" id="longitude" name="venue[longitude]" value="{{ old('venue.longitude') }}">
                        <input type="hidden" id="latitude" name="venue[latitude]" value="{{ old('venue.latitude') }}">
                    </div>
                </div>

                <div class="rel mb2 col col-6 sm-col-3 px1">
                    <label for="s_date" class="caps h5 abs">Start Date</label>
                    <input type="text" class="field col-12 rel z1 date-input" id="s_date" name="s_date" value="{{ old('s_date') }}" autocomplete="off">
                </div>

                <div class="rel mb2 col col-6 sm-col-3 px1">
                    <label for="s_time" class="caps h5 abs">Start Time</label>
                    <input type="text" class="field col-12 rel z1 time-input" id="s_time" name="s_time" value="{{ old('s_time') }}" autocomplete="off">
                </div>

                <div class="rel mb2 col col-6 sm-col-3 px1">
                    <label for="e_date" class="caps h5 abs">End Date</label>
                    <input type="text" class="field col-12 rel z1 date-input" id="e_date" name="e_date" value="{{ old('e_date') }}" autocomplete="off">
                </div>

                <div class="rel mb2 col col-6 sm-col-3 px1">
                    <label for="e_time" class="caps h5 abs">End Time</label>
                    <input type="text" class="field col-12 rel z1 time-input" id="e_time" name="e_time" value="{{ old('e_time') }}" autocomplete="off">
                </div>

                <div class="rel mb2 col col-12 px1 js-field-active">
                    <label class="caps h5" for="tags">Tags</label>
                    <div class="px1 h5">
                        @foreach($tags as $tag)
                            <input type="checkbox" class="tag-checkbox sr-only" id="tag_{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                            <label for="tag_{{ $tag->id }}" class="inl-blk tag b">{{ $tag->name }}</label>
                        @endforeach
                    </div>
                </div>

    {{--
                <div class="rel mb2 col col-12 px1">
                    <ul class="list0 m0 blk col-12 rel z1 tag-list">
                        <label for="tags" class="caps h5 abs">Tags</label>
                        <input type="text" class="field tag-input" id="tags" name="tags" value="{{ old('tags') }}" autocomplete="off">
                    </ul>
                </div>


                <div class="rel col col-12 px1">
                    <label for="hashtag" class="caps h5 abs">Hashtag</label>
                    <input type="text" class="field col-12 rel z1" id="hashtag" name="hashtag" value="{{ old('hashtag') }}">
                </div>
    --}}

                <div class="rel mb2 col col-12 px1">
                    <label for="description" class="caps h5 abs">Description</label>
                    <textarea id="description" class="field col-12 rel z1" name="description" rows="1">{{ old('description') }}</textarea>
                </div>

                <div class="rel mb2 col col-12 px1">
                    <button type="submit" class="btn caps blk col-12">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
