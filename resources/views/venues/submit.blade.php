@extends('app')

@section('title', 'Venues')
@section('description', '')

@section('content')
    <div class="px2 py4 container clearfix">
        <form method="post" action="{{ action('VenueController@store') }}">
            <div class="form-head">
                @include('errors.form')
            </div>
            <div class="form-body sm-mxn1">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <button type="button" class="btn right bg-light-gray" id="btn_facebook">Pull Facebook Info</button>
                    <div class="o-hidden">
                        <label for="fb_url" class="caps h5 abs">Facebook URL</label>
                        <input type="url" data-url="fb-page" class="blk field col-12" id="fb_url" name="fb_url" value="{{ old('fb_url', $fb_url) }}">
                        <input type="hidden" id="facebook" name="facebook" value="{{ old('facebook') }}">
                    </div>
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label for="name" class="caps h5 abs">Name</label>
                    <input type="text" class="blk field col-12" id="name" name="name" value="{{ old('name') }}">
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <button type="button" class="btn right bg-light-gray" id="btn_map">Map It</button>
                    <div class="o-hidden">
                        <label for="street" class="caps h5 abs">Street</label>
                        <input type="text" class="blk field col-12" id="street" name="street" value="{{ old('street') }}">
                    </div>
                </div>

                <input type="hidden" id="city" name="city" value="{{ old('city') }}">
                <input type="hidden" id="state" name="state" value="{{ old('state') }}">
                <input type="hidden" id="zip" name="zip" value="{{ old('zip') }}">
                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">

                <div class="rel mb2 col col-12 px1 js-field-active">
                    <label class="caps h5" for="tags">Tags</label>
                    <div class="px1 h5">
                        @foreach($tags as $tag)
                            <input type="checkbox" class="tag-checkbox sr-only" id="tag_{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                            <label for="tag_{{ $tag->id }}" class="inl-blk tag b">{{ $tag->name }}</label>
                        @endforeach
                    </div>
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label for="twitter" class="caps h5 abs">Twitter Handle</label>
                    <input type="text" class="blk field col-12" id="twitter" name="twitter" value="{{ old('twitter') }}">
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label for="website" class="caps h5 abs">Website</label>
                    <input type="url" class="blk field col-12" id="website" name="website" value="{{ old('website') }}">
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label for="email" class="caps h5 abs">Email</label>
                    <input type="email" class="blk field col-12" id="email" name="email" value="{{ old('email') }}">
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label for="phone" class="caps h5 abs">Phone</label>
                    <input type="tel" class="blk field col-12" id="phone" name="phone" value="{{ old('phone') }}">
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label for="description" class="caps h5 abs">Description</label>
                    <textarea id="description" class="blk field col-12" name="description" rows="1">{{ old('description') }}</textarea>
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <button type="submit"  class="btn blk col-12">Create</button>
                </div>
            </div>
        </form>
    </div>
@endsection