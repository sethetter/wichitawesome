var jQuery = require('jquery');
module.exports = (function($, undefined) {
    'use strict';

    // Stores the promise after the map has been initialized once
    var initialized = null; 
    var geocoder = null;
    var maps = [];

    var loadApi = function(options) {

        if(initialized) {
            return initialized;
        }

        var defaults = {
            version: '3',
            apiKey: 'AIzaSyBfusPeEkmONUSeSDFTXg1YuJD7brTbbM8'
        };
        this.settings = $.extend( {}, defaults, options );

        var deferred = $.Deferred();

         // check if Google Maps API has been loaded
        if (typeof window.google !== 'undefined' && typeof window.google.maps !== 'undefined') {
            deferred.resolve(window.google.maps);
            return deferred.promise();
        }

        // TODO: is this the simplest way to generate a random number?
        var randomizedFunctionName = "onGoogleMapsReady_" + Math.round(Math.random()*1000);

        window[randomizedFunctionName] = function() {
            deferred.resolve(window.google.maps);

            // delete callback once fired
            // TODO: is this necessary?
            setTimeout(function() {
                try {
                    delete window[randomizedFunctionName];
                } catch (ignore) {}
            }, 20);
        };

        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=' + this.settings.version + 
            '&callback=' + randomizedFunctionName;
        document.body.appendChild(script);

        deferred.done(function(){
            geocoder = new google.maps.Geocoder();
        });

        // When the API has loaded fire the callback
        initialized = deferred.promise();
        return initialized;
    };
    var setMap = function(elementId, options) {
        var defaults = {
            center: new google.maps.LatLng(37.699011,-97.3439585),
            zoom: 16,
            mapTypeControl: false,
            streetViewControl: false,
            styles: [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#98d8e2"},{"visibility":"on"}]}]
        };
        var mapOptions = $.extend( {}, defaults, options );
        var map = new google.maps.Map(document.getElementById(elementId), mapOptions);
        map.bounds = new google.maps.LatLngBounds();
        map.markers = [];
        map.setMarker = function(latLng) {
            var pin = {
                path: 'M16,0C7.2,0,0,7.2,0,16c0,11.1,9.7,11.8,16,28c6.2-16,16-15.9,16-28C32,7.2,24.8,0,16,0z M16,23 c-3.9,0-7-3.1-7-7c0-3.9,3.1-7,7-7s7,3.1,7,7C23,19.9,19.9,23,16,23z',
                strokeColor: 'transparent',
                fillColor: '#ec1d36',
                fillOpacity: 1,
                anchor: new google.maps.Point(16,44)
            };
            this.markers.push(new google.maps.Marker({
                map: map,
                position: latLng,
                icon: pin,
                animation: google.maps.Animation.DROP
            }));
            this.bounds.extend(latLng);
        };
        map.destroyMarkers = function() {
            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null);
            }
            this.markers = [];
        }
        maps[elementId] = map;
        return map;
    };
    var getMap = function(id) {
        return maps[id];
    };
    var geocodeAddress = function(str, callback) {
        geocoder.geocode({'address': str}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                callback(results);
            } else {
                console.error('Geocoder Error: ' + status);
                return;
            }
        });
    };
    var geocodeToVenue = function(str, callback) {
        this.geocodeAddress(str, function(data) {
            var components = {}; 
            var venue = {};
            var location = data[0].geometry.location;
            $.each(data[0].address_components, function(k, v1) {
                $.each(v1.types, function(k2, v2) {
                    // Grab the short name for states
                    components[v2] = (v2 !== 'administrative_area_level_1') ? v1.long_name : v1.short_name;
                });
            });
            venue.street = components.street_number + ' ' + components.route;
            venue.city = components.locality;
            venue.state = components.administrative_area_level_1;
            venue.zip = components.postal_code;
            venue.longitude = location.lng();
            venue.latitude = location.lat();

            callback(venue);
        });
    };

    return {
        loadApi: loadApi,
        setMap: setMap,
        getMap: getMap,
        geocodeAddress: geocodeAddress,
        geocodeToVenue: geocodeToVenue,
    };
}(jQuery));