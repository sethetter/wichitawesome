@extends('app')

@section('title', 'Venues')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('VenueController@update', $venue->id) }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="put">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="name">Name</label>
                <input type="text" class="blk col-12 mb1 field" id="name" name="name" value="{{ old('name', $venue->name) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="button" class="btn right" id="btn_map">Map It</button>
                <div class="o-hidden">
                    <label class="caps h5 abs" for="street">Street</label>
                    <input type="text" class="blk col-12 mb1 field" id="street" name="street" value="{{ old('street', $venue->street) }}">
                </div>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="city">City</label>
                <input type="text" class="blk col-12 mb1 field" id="city" name="city" value="{{ old('city', $venue->city) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="state">State</label>
                <input type="text" class="blk col-12 mb1 field" id="state" name="state" value="{{ old('state', $venue->state) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="zip">Zip</label>
                <input type="text" class="blk col-12 mb1 field" id="zip" name="zip" value="{{ old('zip', $venue->zip) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="longitude">Longitude</label>
                <input type="text" class="blk col-12 mb1 field" id="longitude" name="longitude" value="{{ old('longitude', $venue->longitude) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="latitude">Latitude</label>
                <input type="text" class="blk col-12 mb1 field" id="latitude" name="latitude" value="{{ old('latitude', $venue->latitude) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="facebook">Facebook ID</label>
                <input type="text" class="blk col-12 mb1 field" id="facebook" name="facebook" value="{{ old('facebook', $venue->facebook) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="twitter">Twitter Handle</label>
                <input type="text" class="blk col-12 mb1 field" id="twitter" name="twitter" value="{{ old('twitter', $venue->twitter) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="website">Website</label>
                <input type="text" class="blk col-12 mb1 field" id="website" name="website" value="{{ old('website', $venue->website) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="email">Email</label>
                <input type="text" class="blk col-12 mb1 field" id="email" name="email" value="{{ old('email', $venue->email) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="phone">Phone</label>
                <input type="text" class="blk col-12 mb1 field" id="phone" name="phone" value="{{ old('phone', $venue->phone) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="description">Description</label>
                <textarea class="blk col-12 mb1 field" id="description" name="description">{{ old('description', $venue->description) }}</textarea>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label><input type="radio" id="visible_true" name="visible" value="1" {{ (old('visible', $venue->visible) == 1) ? 'checked' : '' }}>Show</label>
                <label><input type="radio" id="visible_false" name="visible" value="0" {{ (old('visible', $venue->visible) == 0) ? 'checked' : '' }}>Hide</label>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="submit" class="btn blk col-12">Update</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(function(){
            maps.loadApi();

            // TODO: clean up this plugin code up.
            $(window).load(function() {
                window.autosize(form.inputs.description);
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