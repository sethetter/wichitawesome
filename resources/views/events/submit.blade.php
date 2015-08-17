@extends('app')

@section('title', 'An Awesome New Event')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('EventController@collect') }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body sm-mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="button" class="btn caps right" id="btn_facebook">Pull Facebook Info</button>
                <div class="o-hidden">
                    <label for="fb_url" class="caps h5 abs">Facebook URL</label>
                    <input type="text" class="blk field col-12" id="fb_url" name="fb_url" value="{{ old('fb_url') }}" autofocus>
                    <input type="hidden" id="facebook" name="facebook" value="{{ old('facebook') }}">
                </div>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="name" class="caps h5 abs">Name</label>
                <input type="text" class="field blk col-12" id="name" name="name" value="{{ old('name') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="button" class="btn caps right" id="btn_map">Map It</button>
                <div class="o-hidden">
                    <label for="venue_name" class="caps h5 abs">Location</label>
                    <input type="text" class="field col-12" id="venue_name" name="venue[name]" value="{{ old('venue.name') }}">
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

            <div class="rel mb2 sm-col sm-col-6 sm-px1">
                <label for="s_date" class="caps h5 abs">Start Date</label>
                <input type="text" class="field col-12 date-input" id="s_date" name="s_date" value="{{ old('s_date') }}" autocomplete="off">
            </div>

            <div class="rel mb2 sm-col sm-col-6 sm-px1">
                <label for="s_time" class="caps h5 abs">Start Time</label>
                <input type="text" class="field col-12 time-input" id="s_time" name="s_time" value="{{ old('s_time') }}" autocomplete="off">
            </div>

            <div class="rel mb2 sm-col sm-col-6 sm-px1">
                <label for="e_date" class="caps h5 abs">End Date</label>
                <input type="text" class="field col-12 date-input" id="e_date" name="e_date" value="{{ old('e_date') }}" autocomplete="off">
            </div>

            <div class="rel mb2 sm-col sm-col-6 sm-px1">
                <label for="e_time" class="caps h5 abs">End Time</label>
                <input type="text" class="field col-12 time-input" id="e_time" name="e_time" value="{{ old('e_time') }}" autocomplete="off">
            </div>

{{--
            <div class="rel sm-col sm-px1">
                <label for="hashtag" class="caps h5 abs">Hashtag</label>
                <input type="text" class="field col-12" id="hashtag" name="hashtag" value="{{ old('hashtag') }}">
            </div>
--}}

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="description" class="caps h5 abs">Description</label>
                <textarea id="description" class="field col-12" name="description" rows="1">{{ old('description') }}</textarea>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="submit" class="btn caps blk col-12">Submit</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(function() {
            maps.loadApi();

            // TODO: clean up this plugin code up.
            window.autosize(form.inputs.description);

            form.enable.dateTime();

            form.inputs.venue_name.autocomplete({
                minLength: 2,
                position: {my: 'center top', at: 'center bottom', collision: 'fit'},
                source: function(request, response) {

                    var term = cache.get(request.term);

                    if(term) {
                        response(term);
                        return;
                    }

                    term = request.term;

                    // TODO: move this out into its own "ict" object and treat it like the fb object
                    $.getJSON(apiUrl + 'venues/location/', {'query': term}, function( data, status, xhr ) {
                        cache.set(term, data);
                        response(data);
                    });
                },
                focus: function(event, ui) {
                    form.inputs.venue_name.val(form.format.address(ui.item));
                    return false;
                },
                select: function( event, ui ) {
                    var venue = ui.item;
                    var address = form.format.address(venue)
                    form.inputs.venue_name.val(address);
                    form.inputs.venue_id.val(venue.id);
                    cache.set(address,  venue);
                    return false;
                }
            });

            form.inputs.venue_name.autocomplete('instance')._renderItem = function(ul, item) {
                return $('<li>' + item.name + '<br><small>' + item.street + ', ' + item.city + ', ' + item.state + '</small></li>')
                    .appendTo(ul);
            };

            form.inputs.venue_name.autocomplete('instance')._resizeMenu = function() {
                var w = this.element.width();
                this.menu.element.outerWidth(w);
            };

            form.inputs.venue_name.blur(function() {
                $('#btn_map').trigger('click');
            });

            $('#btn_map').click(function(){
                var $canvas = $(this).parent().find('#map');
                var street = form.inputs.venue_name.val();
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

            $('#btn_facebook').click(function() {
                form.getEventByFacebook(form.inputs.fb_url.val());
            });

        });
    </script>
@endsection
