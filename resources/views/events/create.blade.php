@extends('app')

@section('title', 'Events')
@section('description', '')

@section('content')
    <div class="px2 py4 container clearfix">
        <form method="post" action="{{ action('EventController@store') }}">
            <div class="form-head">
                @include('errors.form')
            </div>
           <div class="form-body mxn1">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="rel mb2 col col-12 px1">
                    <button type="button" class="btn caps right bg-light-gray" id="btn_facebook">Pull Info</button>
                    <div class="o-hidden">
                        <label for="fb_url"  data-url="fb-event" class="caps h5 abs">Facebook URL</label>
                        <input type="url" class="blk field col-12 rel z1 mb1" id="fb_url" name="fb_url" value="{{ old('fb_url') }}" autofocus>
                        <div class="rel col col-12">
                            <label class="caps h5 abs" for="facebook">Facebook</label>
                            <input type="number" class="blk col-12 rel z1 field" id="facebook" name="facebook" value="{{ old('facebook') }}">
                        </div>
                    </div>
                </div>

                <div class="rel mb2 col col-12 px1">
                    <label class="caps h5 abs" for="name">Name</label>
                    <input type="text" class="blk col-12 rel z1 mb1 field" id="name" name="name" value="{{ old('name') }}">
                </div>

                <div class="rel mb2 col col-6 sm-col-3 px1">
                    <label class="caps h5 abs" for="s_date">Start Date</label>
                    <input type="text" class="blk col-12 rel z1 field date-input" id="s_date" name="s_date" value="{{ old('s_date') }}">
                </div>

                <div class="rel mb2 col col-6 sm-col-3 px1">
                    <label class="caps h5 abs" for="s_time">Start Time</label>
                    <input type="text" class="blk col-12 rel z1 field time-input" id="s_time" name="s_time" value="{{ old('s_time') }}">
                </div>

                <div class="rel mb2 col col-6 sm-col-3 px1">
                    <label class="caps h5 abs" for="e_date">End Date</label>
                    <input type="text" class="blk col-12 rel z1 field date-input" id="e_date" name="e_date" value="{{ old('e_date') }}">
                </div>

                <div class="rel mb2 col col-6 sm-col-3 px1">
                    <label class="caps h5 abs" for="e_time">End Time</label>
                    <input type="text" class="blk col-12 rel z1 field time-input" id="e_time" name="e_time" value="{{ old('e_time') }}">
                </div>

                <div class="rel mb2 col col-12 px1">
                    <button type="button" class="btn caps right bg-light-gray" id="btn_map">Map It</button>
                    <div class="o-hidden">
                        <label for="venue_name" class="caps h5 abs">Location</label>
                        <input type="text" class="field col-12 rel z1 mb2" id="venue_name" name="venue[name]" value="{{ old('venue.name') }}">
                        <div class="rel col col-12">
                            <select class="blk col-12 rel z1 field" id="venue_id" name="venue_id">
                                <option value="">None</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="venue_facebook" name="venue[facebook]" value="{{ old('venue.facebook') }}">
                        <input type="hidden" id="street" name="venue[street]" value="{{ old('venue.street') }}">
                        <input type="hidden" id="city" name="venue[city]" value="{{ old('venue.city') }}">
                        <input type="hidden" id="state" name="venue[state]" value="{{ old('venue.state') }}">
                        <input type="hidden" id="zip" name="venue[zip]" value="{{ old('venue.zip') }}">
                        <input type="hidden" id="longitude" name="venue[longitude]" value="{{ old('venue.longitude') }}">
                        <input type="hidden" id="latitude" name="venue[latitude]" value="{{ old('venue.latitude') }}">
                    </div>
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

                <div class="rel mb2 col col-12 px1">
                    <label class="caps h5 abs" for="hashtag">Hashtag</label>
                    <input type="text" class="blk col-12 rel z1 field" id="hashtag" name="hashtag" value="{{ old('hashtag') }}">
                </div>

                <div class="rel mb2 col col-12 px1">
                    <label class="caps h5 abs" for="description">Description</label>
                    <textarea class="blk col-12 rel z1 field" rows="1" id="description" name="description">{{ old('description') }}</textarea>
                </div>

                <div class="rel mb2 col col-12 px1">
                    <button type="submit" class="btn blk col-12">Update</button>
                </div>
            </div>
        </form>
    </div>
@endsection