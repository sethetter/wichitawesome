var fb = require('./fb');

// TODO: This class needs to be re-factored so that it is more flexible.
//       The inputs object should be dynamic so that field names can change and 
//       we don't have to load every input even if it doesn't exists.
var form = {
    inputs: {
        name:           $('#name'),
        fb_url:         $('#fb_url'),
        facebook:       $('#facebook'),
        venue_name:     $('#venue_name'),
        venue_id:       $('#venue_id'),
        venue_facebook: $('#venue_facebook'),
        longitude:      $('#longitude'),
        latitude:       $('#latitude'),
        street:         $('#street'),
        city:           $('#city'),
        state:          $('#state'),
        zip:            $('#zip'),
        start_date:     $('#s_date'),
        start_time:     $('#s_time'),
        end_date:       $('#e_date'),
        end_time:       $('#e_time'),
        phone:          $('#phone'),
        description:    $('#description')
    },
    format: {
        strToDate: function(isostr) {
            var parts = isostr.match(/\d+/g);
            return new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4], parts[5]);
        },
        timeToStr: function(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        },
        dateToStr: function(date) {
            return (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
        },
        address: function(obj) {
            return obj.street + ' ' + obj.city + ', ' + obj.state + ' ' + obj.zip
        }
    },
    // TODO: Make this function better, it won't work right if there are multiple forms on the same page.
    //       We should probably re-factor this class to work with multiple intances
    displayError: function(message) {
        $('.form-head').append('<div class="p1 mb2 h5 font-heading white bg-dark-red">' + message +'</div>');
    },
    positionLabels: function() {
        $('.field').each(function() {
            if (this.value != '') {
                $(this).parent().addClass('field-active');
            }
        });
    },
    expandDescription: function() {

        // trigger textarea keyup so it expands
        var evt = document.createEvent('Event');
        evt.initEvent('autosize.update', true, false);
        form.inputs.description[0].dispatchEvent(evt);
    },
    enable: {
        dateTime: function() {
            // build an array of time increments
            var timeOptions = [];
            var h = 1;
            var i = 0;

            for(h = 1; h <= 12; h++) {
                for(i = 0; i <= 55; i += 5) {

                    // force a 0 before minutes
                    var t = h + ':' + ('0' + i).slice(-2);
                    timeOptions.push(t + ' am');
                    timeOptions.push(t + ' pm');
                }
            };

            $('.time-input').autocomplete({
                minLength: 0,
                delay: 0,
                source: function (request, response) {
                    var matches = $.map(timeOptions, function (option) {
                        if (option.toUpperCase().indexOf(request.term.toUpperCase()) === 0) {
                            return option;
                        }
                    });
                    response(matches.slice(0, 8));
                },
                position: { my: 'center top', at: 'center bottom', collision: 'none' }
            });

            $('.time-input').each(function(){
                $(this).autocomplete('instance')._resizeMenu = function() {
                    var w = this.element.outerWidth();
                    this.menu.element.outerWidth(w);
                }
            });

            $('.date-input').datepicker({ 
                minDate: 0,
                prevText: '',
                nextText: '',
                onSelect: function () { this.focus(); $(this).parent('div').addClass('field-active'); },
                onClose: function () { this.focus(); }
            });
        },
    },
    setVenueLocation: function(venue) {
        this.inputs.street.val(venue.street);
        this.inputs.city.val(venue.city);
        this.inputs.state.val(venue.state);
        this.inputs.zip.val(venue.zip);
        this.inputs.longitude.val(venue.longitude);
        this.inputs.latitude.val(venue.latitude);

        var $canvas = $('#btn_map').parent().find('#map');
        if(!$canvas.length) {
            $('#btn_map').parent().append('<div id="map" class="col-12 mb1" style="height:200px;">');
        }

        var latLng = new google.maps.LatLng(venue.latitude, venue.longitude);
        var map = maps.setMap('map', {center: latLng});
        map.destroyMarkers();
        map.setMarker(latLng);

        form.positionLabels();
        form.expandDescription();
    },
    setEventInfo: function(ictEvent) {
        form.inputs.facebook.val(ictEvent.id);
        form.inputs.name.val(ictEvent.name);
        form.inputs.start_date.val(form.format.dateToStr(ictEvent.start_time));
        form.inputs.start_time.val(form.format.timeToStr(ictEvent.start_time));
        if(ictEvent.end_time) {
            form.inputs.end_date.val(form.format.dateToStr(ictEvent.end_time));
            form.inputs.end_time.val(form.format.timeToStr(ictEvent.end_time));
        }
        form.inputs.description.val(ictEvent.description);

        form.positionLabels();
        form.expandDescription();
    },
    getEventByFacebook: function(str) {

        if ( ! str) {
            return true;
        }

        // if the string is a facebook url
        if(str.indexOf('//www.facebook.com/') > -1) {
            // get the page id segment from the URL
            str = str.split('/')[4];
        }

        var ictEvent = cache.get(str);

        if(ictEvent != null && ictEvent.length > 0) {
            form.setEventInfo(ictEvent);
        };

        fb.pageToEvent(str)
            .fail(function() {
                form.displayError('<strong>Sorry!</strong> We couldn\'t find any info. The event might be private, or this website might just be dumb.');
            })
            .done(function(newEvent) {
                newEvent.start_time = form.format.strToDate(newEvent.start_time);
                newEvent.end_time = newEvent.end_time ? form.format.strToDate(newEvent.end_time) : null;
                // TODO: implement caching up in here.
                cache.set(str, newEvent);
                form.setEventInfo(newEvent);
                form.positionLabels();

                // Check if there is a Facebook venue
                if (newEvent.place) {
                    // Check if we have a matching venue in our database
                    // TODO: Check the cache before hitting the ICT API
                    $.getJSON(apiUrl + 'venues', {'facebook': newEvent.place.id}, function( venues, status, xhr ) {
                        // If an array of venues was returned set the first
                        if(venues.length > 0) {
                            form.inputs.venue_name.val(venues[0].street).parent().addClass('field-active');
                            form.inputs.venue_id.val(venues[0].id);
                            form.setVenueLocation(venues[0]);
                        } else {
                            // If no matches in our database geocode the facebook info
                            form.inputs.venue_name.val(newEvent.place.name).parent().addClass('field-active');
                            form.inputs.venue_facebook.val(newEvent.place.id);
                            if(newEvent.place.location) {
                                form.setVenueLocation(newEvent.place.location);
                            } else {
                                maps.geocodeToVenue(newEvent.place.name + ' Wichita, KS', function(venue){
                                    form.setVenueLocation(venue);
                                });
                            }
                        }
                    });
                }
            });
    },
    getVenueByFacebook: function(str) {

        if ( ! str) {
            return true;
        }

        // if the string is a facebook url
        if(str.indexOf('//www.facebook.com/') > -1) {

            // get the page id segment from the URL
            str = str.split('/')[3];
        }

        var cachedVenue = cache.get(str);

        if(cachedVenue) {
            form.inputs.name.val(cachedVenue.name);
            form.inputs.facebook.val(cachedVenue.id);
            form.inputs.phone.val(cachedVenue.phone);
            form.inputs.description.val(cachedVenue.about);

            form.setVenueLocation(cachedVenue.location);
        };

        fb.pageToVenue(str)
            .fail(function() {
                form.displayError('<strong>Sorry!</strong> We couldn\'t find any info. The page might be private, or this website might just be dumb.');
            })
            .done(function(venue) {

                cache.set(str, venue);

                form.inputs.name.val(venue.name);
                form.inputs.facebook.val(venue.id);
                form.inputs.phone.val(venue.phone);
                form.inputs.description.val(venue.about);

                form.setVenueLocation(venue.location);
            });

        form.expandDescription();
    }
};

module.exports = form;

$(window).load(function() {
    form.positionLabels();
});

$('.field')
    .on('change cut paste input keyup', function() {
        $(this).parent().addClass('field-active');
    })
    .on('blur', '', function() {
        if (this.value == '') {
            $(this).parent().removeClass('field-active');
        }
    });