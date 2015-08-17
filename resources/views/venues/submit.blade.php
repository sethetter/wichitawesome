@extends('app')

@section('title', 'Venues')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('VenueController@store') }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body sm-mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="button" class="btn right" id="btn_facebook">Pull Facebook Info</button>
                <div class="o-hidden">
                    <label for="fb_url" class="caps h5 abs">Facebook URL</label>
                    <input type="text" class="blk field col-12" id="fb_url" name="fb_url" value="{{ old('fb_url') }}">
                </div>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="name" class="caps h5 abs">Name</label>
                <input type="text" class="blk field col-12" id="name" name="name" value="{{ old('name') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="button" class="btn right" id="btn_map">Map It</button>
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
            <input type="hidden" id="facebook" name="facebook" value="{{ old('facebook') }}">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="twitter" class="caps h5 abs">Twitter Handle</label>
                <input type="text" class="blk field col-12" id="twitter" name="twitter" value="{{ old('twitter') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="website" class="caps h5 abs">Website</label>
                <input type="text" class="blk field col-12" id="website" name="website" value="{{ old('website') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="email" class="caps h5 abs">Email</label>
                <input type="text" class="blk field col-12" id="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="phone" class="caps h5 abs">Phone</label>
                <input type="text" class="blk field col-12" id="phone" name="phone" value="{{ old('phone') }}">
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
@endsection

@section('scripts')
    <script>
        $(function(){
            maps.loadApi();

            // TODO: clean up this plugin code up.
            window.autosize(form.inputs.description);

            $('#btn_facebook').click(function() {
                form.getVenueByFacebook(form.inputs.fb_url.val());
            });

            form.inputs.venue_name.blur(function() {
                $('#btn_map').trigger('click');
            });

            $('#btn_map').click(function() {
                var $canvas = $(this).parent().find('#map');
                var street = form.inputs.street.val();
                var cachedVenue = cache.get(street);

                if(!$canvas.length) {
                    $(this).parent().append('<div id="map" class="col-12 mb1" style="height:200px;">');
                }

                if(cachedVenue != null && cachedVenue.length > 0) {
                    form.setVenueLocation(cachedVenue);
                    return;
                }
        
                maps.geocodeToVenue(street + ' Wichita, KS', function(venue){
                    cache.set(street, venue);
                    form.setVenueLocation(venue);
                });
            });
        });
    </script>
@endsection