(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

require('./vendor/jquery-ui.min');

window.cache = require('./cache');
window.maps = require('./maps');
window.form = require('./form');
window.autosize = require('./vendor/autosize');
window.scrollFrame = require('./vendor/scroll-frame');
window.scrollawesome = require('./scrollawesome');

window.apiUrl = 'http://' + window.location.hostname + '/api/';

},{"./cache":2,"./form":4,"./maps":5,"./scrollawesome":6,"./vendor/autosize":7,"./vendor/jquery-ui.min":8,"./vendor/scroll-frame":9}],2:[function(require,module,exports){
"use strict";

module.exports = {
    items: [],
    get: function get(key) {
        if (key in this.items) {
            return this.items[key];
        }
        return null;
    },
    set: function set(key, item) {
        this.items[key] = item;
    }
};

},{}],3:[function(require,module,exports){
'use strict';

module.exports = {
    version: 'v2.4',
    token: '1450071418617846|xH9wnEYA25GVQYGBfgHGYJfWGaA',
    pageToVenue: function pageToVenue(venueId) {
        return $.getJSON('https://graph.facebook.com/' + this.version + '/' + venueId, { fields: 'name,location,phone,about,website', access_token: this.token }).done(function (venue) {
            if (venue.hasOwnProperty('website')) {
                venue.website = venue.website.replace('www.', '');
            }
        });
    },
    pageToEvent: function pageToEvent(eventId) {
        return $.getJSON('https://graph.facebook.com/' + this.version + '/' + eventId, { access_token: this.token });
    }
};

},{}],4:[function(require,module,exports){
'use strict';

var fb = require('./fb');

// TODO: This class needs to be re-factored so that it is more flexible.
//       The inputs object should be dynamic so that field names can change and
//       we don't have to load every input even if it doesn't exists.
var form = {
    inputs: {
        name: $('#name'),
        fb_url: $('#fb_url'),
        facebook: $('#facebook'),
        venue_name: $('#venue_name'),
        venue_id: $('#venue_id'),
        venue_facebook: $('#venue_facebook'),
        longitude: $('#longitude'),
        latitude: $('#latitude'),
        street: $('#street'),
        city: $('#city'),
        state: $('#state'),
        zip: $('#zip'),
        start_date: $('#s_date'),
        start_time: $('#s_time'),
        end_date: $('#e_date'),
        end_time: $('#e_time'),
        phone: $('#phone'),
        website: $('#website'),
        description: $('#description')
    },
    format: {
        strToDate: function strToDate(isostr) {
            var parts = isostr.match(/\d+/g);
            return new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4], parts[5]);
        },
        timeToStr: function timeToStr(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        },
        dateToStr: function dateToStr(date) {
            return date.getMonth() + 1 + '/' + date.getDate() + '/' + date.getFullYear();
        },
        address: function address(obj) {
            return obj.street + ' ' + obj.city + ', ' + obj.state + ' ' + obj.zip;
        }
    },
    // TODO: Make this function better, it won't work right if there are multiple forms on the same page.
    //       We should probably re-factor this class to work with multiple intances
    displayError: function displayError(message) {
        $('.form-head').empty().append('<div class="p1 mb2 h5 font-heading white bg-dark-red">' + message + '</div>');
    },
    positionLabels: function positionLabels() {
        $('.field').each(function () {
            if (this.value != '') {
                $(this).parent().addClass('field-active');
            }
        });
    },
    expandDescription: function expandDescription() {

        // trigger textarea keyup so it expands
        var evt = document.createEvent('Event');
        evt.initEvent('autosize.update', true, false);
        form.inputs.description[0].dispatchEvent(evt);
    },
    enable: {
        dateTime: function dateTime() {
            // build an array of time increments
            var timeOptions = [];
            var h = 1;
            var i = 0;

            for (h = 1; h <= 12; h++) {
                for (i = 0; i <= 55; i += 5) {

                    // force a 0 before minutes
                    var t = h + ':' + ('0' + i).slice(-2);
                    timeOptions.push(t + ' am');
                    timeOptions.push(t + ' pm');
                }
            };

            $('.time-input').autocomplete({
                minLength: 0,
                delay: 0,
                source: function source(request, response) {
                    var matches = $.map(timeOptions, function (option) {
                        if (option.toUpperCase().indexOf(request.term.toUpperCase()) === 0) {
                            return option;
                        }
                    });
                    response(matches.slice(0, 8));
                },
                position: { my: 'center top', at: 'center bottom', collision: 'none' }
            });

            $('.time-input').each(function () {
                $(this).autocomplete('instance')._resizeMenu = function () {
                    var w = this.element.outerWidth();
                    this.menu.element.outerWidth(w);
                };
            });

            $('.date-input').datepicker({
                minDate: 0,
                prevText: '',
                nextText: '',
                onSelect: function onSelect() {
                    this.focus();$(this).parent('div').addClass('field-active');
                },
                onClose: function onClose() {
                    this.focus();
                }
            });
        }
    },
    setVenueLocation: function setVenueLocation(venue) {
        this.inputs.street.val(venue.street);
        this.inputs.city.val(venue.city);
        this.inputs.state.val(venue.state);
        this.inputs.zip.val(venue.zip);
        this.inputs.longitude.val(venue.longitude);
        this.inputs.latitude.val(venue.latitude);

        var $canvas = $('#btn_map').parent().find('#map');
        if (!$canvas.length) {
            $('#btn_map').parent().append('<div id="map" class="col-12 mb1" style="height:200px;">');
        }

        var latLng = new google.maps.LatLng(venue.latitude, venue.longitude);
        var map = maps.setMap('map', { center: latLng });
        map.destroyMarkers();
        map.setMarker(latLng);

        form.positionLabels();
        form.expandDescription();
    },
    setEventInfo: function setEventInfo(ictEvent) {
        form.inputs.facebook.val(ictEvent.id);
        form.inputs.name.val(ictEvent.name);
        form.inputs.start_date.val(form.format.dateToStr(ictEvent.start_time));
        form.inputs.start_time.val(form.format.timeToStr(ictEvent.start_time));
        if (ictEvent.end_time) {
            form.inputs.end_date.val(form.format.dateToStr(ictEvent.end_time));
            form.inputs.end_time.val(form.format.timeToStr(ictEvent.end_time));
        }
        form.inputs.description.val(ictEvent.description);

        form.positionLabels();
        form.expandDescription();
    },
    getEventByFacebook: function getEventByFacebook(str) {

        if (!str) {
            return true;
        }

        // if the string is a facebook url
        if (str.indexOf('//www.facebook.com/') > -1) {
            // get the page id segment from the URL
            str = str.split('/')[4];
        }

        var ictEvent = cache.get(str);

        if (ictEvent != null && ictEvent.length > 0) {
            form.setEventInfo(ictEvent);
        };

        fb.pageToEvent(str).fail(function () {
            form.displayError('<strong>Sorry!</strong> We couldn\'t find any info. The event might be private, or this website might just be dumb.');
        }).done(function (newEvent) {
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
                $.getJSON(apiUrl + 'venues', { 'facebook': newEvent.place.id }, function (venues, status, xhr) {
                    // If an array of venues was returned set the first
                    if (venues.length > 0) {
                        form.inputs.venue_name.val(venues[0].street).parent().addClass('field-active');
                        form.inputs.venue_id.val(venues[0].id);
                        form.setVenueLocation(venues[0]);
                    } else {
                        // If no matches in our database geocode the facebook info
                        form.inputs.venue_name.val(newEvent.place.name).parent().addClass('field-active');
                        form.inputs.venue_facebook.val(newEvent.place.id);
                        if (newEvent.place.location) {
                            form.setVenueLocation(newEvent.place.location);
                        } else {
                            maps.geocodeToVenue(newEvent.place.name + ' Wichita, KS', function (venue) {
                                form.setVenueLocation(venue);
                            });
                        }
                    }
                });
            }
        });
    },
    getVenueByFacebook: function getVenueByFacebook(str) {

        if (!str) {
            return true;
        }

        // if the string is a facebook url
        if (str.indexOf('//www.facebook.com/') > -1) {
            // get the page id segment from the URL
            if (str.indexOf('/pages/') === -1) {
                str = str.split('/')[3];
            } else {
                str = str.split('/')[5];
            }
        }

        var cachedVenue = cache.get(str);

        if (cachedVenue) {
            form.inputs.name.val(cachedVenue.name);
            form.inputs.facebook.val(cachedVenue.id);
            form.inputs.phone.val(cachedVenue.phone);
            form.inputs.website.val(cachedVenue.website);
            form.inputs.description.val(cachedVenue.about);

            form.setVenueLocation(cachedVenue.location);
        };

        fb.pageToVenue(str).fail(function () {
            form.displayError('<strong>Sorry!</strong> We couldn\'t find any info. The page might be private, or this website might just be dumb.');
        }).done(function (venue) {

            cache.set(str, venue);

            form.inputs.name.val(venue.name);
            form.inputs.facebook.val(venue.id);
            form.inputs.phone.val(venue.phone);
            form.inputs.website.val(venue.website);
            form.inputs.description.val(venue.about);

            form.setVenueLocation(venue.location);
        });

        form.expandDescription();
    }
};

module.exports = form;

$(window).load(function () {
    form.positionLabels();
});

$('.field').on('change cut paste input keyup', function () {
    $(this).parent().addClass('field-active');
}).on('blur', '', function () {
    if (this.value == '') {
        $(this).parent().removeClass('field-active');
    }
});

},{"./fb":3}],5:[function(require,module,exports){
'use strict';

module.exports = (function ($, undefined) {
    'use strict';

    // Stores the promise after the map has been initialized once
    var initialized = null;
    var geocoder = null;
    var maps = [];

    var loadApi = function loadApi(options) {

        if (initialized) {
            return initialized;
        }

        var defaults = {
            version: '3',
            apiKey: 'AIzaSyBfusPeEkmONUSeSDFTXg1YuJD7brTbbM8'
        };
        this.settings = $.extend({}, defaults, options);

        var deferred = $.Deferred();

        // check if Google Maps API has been loaded
        if (typeof window.google !== 'undefined' && typeof window.google.maps !== 'undefined') {
            deferred.resolve(window.google.maps);
            return deferred.promise();
        }

        // TODO: is this the simplest way to generate a random number?
        var randomizedFunctionName = "onGoogleMapsReady_" + Math.round(Math.random() * 1000);

        window[randomizedFunctionName] = function () {
            deferred.resolve(window.google.maps);

            // delete callback once fired
            // TODO: is this necessary?
            setTimeout(function () {
                try {
                    delete window[randomizedFunctionName];
                } catch (ignore) {}
            }, 20);
        };

        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=' + this.settings.version + '&callback=' + randomizedFunctionName;
        document.body.appendChild(script);

        deferred.done(function () {
            geocoder = new google.maps.Geocoder();
        });

        // When the API has loaded fire the callback
        initialized = deferred.promise();
        return initialized;
    };
    var setMap = function setMap(elementId, options) {
        var defaults = {
            center: new google.maps.LatLng(37.699011, -97.3439585),
            zoom: 14,
            mapTypeControl: false,
            streetViewControl: false,
            styles: [{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [{ "color": "#444444" }] }, { "featureType": "landscape", "elementType": "all", "stylers": [{ "color": "#f2f2f2" }] }, { "featureType": "poi", "elementType": "all", "stylers": [{ "visibility": "off" }] }, { "featureType": "road", "elementType": "all", "stylers": [{ "saturation": -100 }, { "lightness": 45 }] }, { "featureType": "road.highway", "elementType": "all", "stylers": [{ "visibility": "simplified" }] }, { "featureType": "road.arterial", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "transit", "elementType": "all", "stylers": [{ "visibility": "off" }] }, { "featureType": "water", "elementType": "all", "stylers": [{ "color": "#98d8e2" }, { "visibility": "on" }] }]
        };
        var mapOptions = $.extend({}, defaults, options);
        var map = new google.maps.Map(document.getElementById(elementId), mapOptions);
        map.bounds = new google.maps.LatLngBounds();
        map.markers = [];
        map.setMarker = function (latLng) {
            this.markers.push(new google.maps.Marker({
                map: map,
                position: latLng,
                icon: '//' + window.location.hostname + '/img/map/pin.svg',
                animation: google.maps.Animation.DROP
            }));
            this.bounds.extend(latLng);
        };
        map.destroyMarkers = function () {
            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null);
            }
            this.markers = [];
        };
        maps[elementId] = map;
        return map;
    };
    var getMap = function getMap(id) {
        return maps[id];
    };
    var geocodeAddress = function geocodeAddress(str, callback) {
        geocoder.geocode({ 'address': str }, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                callback(results);
            } else {
                console.error('Geocoder Error: ' + status);
                return;
            }
        });
    };
    var geocodeToVenue = function geocodeToVenue(str, callback) {
        this.geocodeAddress(str, function (data) {
            var components = {};
            var venue = {};
            var location = data[0].geometry.location;
            $.each(data[0].address_components, function (k, v1) {
                $.each(v1.types, function (k2, v2) {
                    // Grab the short name for states
                    components[v2] = v2 !== 'administrative_area_level_1' ? v1.long_name : v1.short_name;
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
        geocodeToVenue: geocodeToVenue
    };
})(jQuery);

},{}],6:[function(require,module,exports){
"use strict";

module.exports = function (undefined) {
    function documentHeight() {
        return Math.max(document.documentElement.clientHeight, document.body.scrollHeight, document.documentElement.scrollHeight, document.body.offsetHeight, document.documentElement.offsetHeight);
    }
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
    var scroll = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.oRequestAnimationFrame ||
    // IE Fallback, you can even fallback to onscroll
    function (callback) {
        window.setTimeout(callback, 1000 / 60);
    };

    var startSpace = 16;
    var stopSpace = 64;
    var lastPosition = -1;
    var elements;
    var matrix = [];
    var eventList = document.getElementById('event_list');
    var pagination = document.getElementById('pagination_next');
    var paginationStart;
    var currentPage = getParameterByName('page') || 1;
    var loadingPage = false;

    var loop = function loop() {
        var scrollY = window.pageYOffset;

        if (lastPosition == scrollY) {
            scroll(loop);
            return false;
        }

        lastPosition = scrollY;

        var l = matrix.length;
        var i = 0;
        for (i; i < l; i++) {
            if (scrollY >= matrix[i].start) {
                var stop = matrix[i + 1] ? matrix[i + 1].start - stopSpace - matrix[i].height : matrix[i].start;

                matrix[i].el.style['position'] = 'fixed';
                matrix[i].el.style['top'] = startSpace + 'px';

                if (scrollY >= stop) {
                    matrix[i].el.style['position'] = 'absolute';
                    matrix[i].el.style['top'] = stop + startSpace + 'px';
                }
            } else {
                matrix[i].el.style['position'] = 'absolute';
                matrix[i].el.style['top'] = '';
            }

            if (scrollY >= paginationStart && !loadingPage) {
                loadingPage = true;
                currentPage++;
                var url = apiUrl + 'view/events/?page=' + currentPage;
                var req = new XMLHttpRequest();
                req.open('GET', url, true);
                req.onreadystatechange = function () {
                    if (req.status !== 200) {
                        console.log('Status error: ' + req.status);
                        return;
                    }
                    if (req.readyState === 4) {
                        eventList.insertAdjacentHTML('beforeend', req.responseText);
                        pagination.style.display = 'none';
                        refresh();
                    }
                };
                req.send(null);
            }
        };

        scroll(loop);
    };

    var refresh = function refresh() {
        // Convert NodeList into an Array so that we can delete elements
        // without jacking up the Array's indexes
        elements = [].slice.call(document.getElementsByClassName('event-date'));
        var l = elements.length;
        var i = 0; // index for elements
        var j = 0; // index for matrix
        var date = null;
        for (i; i < l; i++) {
            var datetime = elements[i].getAttribute('datetime');
            if (date === datetime.substr(0, datetime.indexOf('T'))) {
                elements[i].parentNode.removeChild(elements[i]);
                continue;
            }

            date = datetime.substr(0, datetime.indexOf('T'));

            matrix[j] = { el: elements[i] };
            matrix[j].el.style['position'] = '';
            matrix[j].el.style['top'] = '';
            matrix[j].height = matrix[j].el.offsetHeight;
            matrix[j].start = matrix[j].el.offsetTop - startSpace;
            j++;
        }
        paginationStart = documentHeight() - window.innerHeight * 2;
        loadingPage = false;
    };

    window.onresize = refresh;

    refresh();
    loop();
};

},{}],7:[function(require,module,exports){
'use strict';

(function (root, factory) {
	'use strict';

	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define([], factory);
	} else if (typeof exports === 'object') {
		// Node. Does not work with strict CommonJS, but
		// only CommonJS-like environments that support module.exports,
		// like Node.
		module.exports = factory();
	} else {
		// Browser globals (root is window)
		root.autosize = factory();
	}
})(undefined, function () {
	function main(ta) {
		if (!ta || !ta.nodeName || ta.nodeName !== 'TEXTAREA' || ta.hasAttribute('data-autosize-on')) {
			return;
		}

		var maxHeight;
		var heightOffset;

		function init() {
			var style = window.getComputedStyle(ta, null);

			if (style.resize === 'vertical') {
				ta.style.resize = 'none';
			} else if (style.resize === 'both') {
				ta.style.resize = 'horizontal';
			}

			// horizontal overflow is hidden, so break-word is necessary for handling words longer than the textarea width
			ta.style.wordWrap = 'break-word';

			// Chrome/Safari-specific fix:
			// When the textarea y-overflow is hidden, Chrome/Safari doesn't reflow the text to account for the space
			// made available by removing the scrollbar. This workaround will cause the text to reflow.
			var width = ta.style.width;
			ta.style.width = '0px';
			// Force reflow:
			/* jshint ignore:start */
			ta.offsetWidth;
			/* jshint ignore:end */
			ta.style.width = width;

			maxHeight = style.maxHeight !== 'none' ? parseFloat(style.maxHeight) : false;

			if (style.boxSizing === 'content-box') {
				heightOffset = -(parseFloat(style.paddingTop) + parseFloat(style.paddingBottom));
			} else {
				heightOffset = parseFloat(style.borderTopWidth) + parseFloat(style.borderBottomWidth);
			}

			adjust();
		}

		function adjust() {
			var startHeight = ta.style.height;
			var htmlTop = document.documentElement.scrollTop;
			var bodyTop = document.body.scrollTop;

			ta.style.height = 'auto';

			var endHeight = ta.scrollHeight + heightOffset;

			if (maxHeight !== false && maxHeight < endHeight) {
				endHeight = maxHeight;
				if (ta.style.overflowY !== 'scroll') {
					ta.style.overflowY = 'scroll';
				}
			} else if (ta.style.overflowY !== 'hidden') {
				ta.style.overflowY = 'hidden';
			}

			ta.style.height = endHeight + 'px';

			// prevents scroll-position jumping
			document.documentElement.scrollTop = htmlTop;
			document.body.scrollTop = bodyTop;

			if (startHeight !== ta.style.height) {
				var evt = document.createEvent('Event');
				evt.initEvent('autosize.resized', true, false);
				ta.dispatchEvent(evt);
			}
		}

		// IE9 does not fire onpropertychange or oninput for deletions,
		// so binding to onkeyup to catch most of those events.
		// There is no way that I know of to detect something like 'cut' in IE9.
		if ('onpropertychange' in ta && 'oninput' in ta) {
			ta.addEventListener('keyup', adjust);
		}

		window.addEventListener('resize', adjust);
		ta.addEventListener('input', adjust);

		ta.addEventListener('autosize.update', adjust);

		ta.addEventListener('autosize.destroy', (function (style) {
			window.removeEventListener('resize', adjust);
			ta.removeEventListener('input', adjust);
			ta.removeEventListener('keyup', adjust);
			ta.removeEventListener('autosize.destroy');

			Object.keys(style).forEach(function (key) {
				ta.style[key] = style[key];
			});

			ta.removeAttribute('data-autosize-on');
		}).bind(ta, {
			height: ta.style.height,
			overflow: ta.style.overflow,
			overflowY: ta.style.overflowY,
			wordWrap: ta.style.wordWrap,
			resize: ta.style.resize
		}));

		ta.setAttribute('data-autosize-on', true);
		ta.style.overflow = 'hidden';
		ta.style.overflowY = 'hidden';

		init();
	}

	// Do nothing in IE8 or lower
	if (typeof window.getComputedStyle !== 'function') {
		return function (elements) {
			return elements;
		};
	} else {
		return function (elements) {
			if (elements && elements.length) {
				Array.prototype.forEach.call(elements, main);
			} else if (elements && elements.nodeName) {
				main(elements);
			}
			return elements;
		};
	}
});

},{}],8:[function(require,module,exports){
/*! jQuery UI - v1.11.4 - 2015-07-19
* http://jqueryui.com
* Includes: core.js, widget.js, position.js, autocomplete.js, datepicker.js, menu.js
* Copyright 2015 jQuery Foundation and other contributors; Licensed MIT */

"use strict";

(function (e) {
  "function" == typeof define && define.amd ? define(["jquery"], e) : e(jQuery);
})(function (e) {
  function t(t, s) {
    var n,
        a,
        o,
        r = t.nodeName.toLowerCase();return "area" === r ? (n = t.parentNode, a = n.name, t.href && a && "map" === n.nodeName.toLowerCase() ? (o = e("img[usemap='#" + a + "']")[0], !!o && i(o)) : !1) : (/^(input|select|textarea|button|object)$/.test(r) ? !t.disabled : "a" === r ? t.href || s : s) && i(t);
  }function i(t) {
    return e.expr.filters.visible(t) && !e(t).parents().addBack().filter(function () {
      return "hidden" === e.css(this, "visibility");
    }).length;
  }function s(e) {
    for (var t, i; e.length && e[0] !== document;) {
      if ((t = e.css("position"), ("absolute" === t || "relative" === t || "fixed" === t) && (i = parseInt(e.css("zIndex"), 10), !isNaN(i) && 0 !== i))) return i;e = e.parent();
    }return 0;
  }function n() {
    this._curInst = null, this._keyEvent = !1, this._disabledInputs = [], this._datepickerShowing = !1, this._inDialog = !1, this._mainDivId = "ui-datepicker-div", this._inlineClass = "ui-datepicker-inline", this._appendClass = "ui-datepicker-append", this._triggerClass = "ui-datepicker-trigger", this._dialogClass = "ui-datepicker-dialog", this._disableClass = "ui-datepicker-disabled", this._unselectableClass = "ui-datepicker-unselectable", this._currentClass = "ui-datepicker-current-day", this._dayOverClass = "ui-datepicker-days-cell-over", this.regional = [], this.regional[""] = { closeText: "Done", prevText: "Prev", nextText: "Next", currentText: "Today", monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"], dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"], weekHeader: "Wk", dateFormat: "mm/dd/yy", firstDay: 0, isRTL: !1, showMonthAfterYear: !1, yearSuffix: "" }, this._defaults = { showOn: "focus", showAnim: "fadeIn", showOptions: {}, defaultDate: null, appendText: "", buttonText: "...", buttonImage: "", buttonImageOnly: !1, hideIfNoPrevNext: !1, navigationAsDateFormat: !1, gotoCurrent: !1, changeMonth: !1, changeYear: !1, yearRange: "c-10:c+10", showOtherMonths: !1, selectOtherMonths: !1, showWeek: !1, calculateWeek: this.iso8601Week, shortYearCutoff: "+10", minDate: null, maxDate: null, duration: "fast", beforeShowDay: null, beforeShow: null, onSelect: null, onChangeMonthYear: null, onClose: null, numberOfMonths: 1, showCurrentAtPos: 0, stepMonths: 1, stepBigMonths: 12, altField: "", altFormat: "", constrainInput: !0, showButtonPanel: !1, autoSize: !1, disabled: !1 }, e.extend(this._defaults, this.regional[""]), this.regional.en = e.extend(!0, {}, this.regional[""]), this.regional["en-US"] = e.extend(!0, {}, this.regional.en), this.dpDiv = a(e("<div id='" + this._mainDivId + "' class='ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>"));
  }function a(t) {
    var i = "button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";return t.delegate(i, "mouseout", function () {
      e(this).removeClass("ui-state-hover"), -1 !== this.className.indexOf("ui-datepicker-prev") && e(this).removeClass("ui-datepicker-prev-hover"), -1 !== this.className.indexOf("ui-datepicker-next") && e(this).removeClass("ui-datepicker-next-hover");
    }).delegate(i, "mouseover", o);
  }function o() {
    e.datepicker._isDisabledDatepicker(u.inline ? u.dpDiv.parent()[0] : u.input[0]) || (e(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover"), e(this).addClass("ui-state-hover"), -1 !== this.className.indexOf("ui-datepicker-prev") && e(this).addClass("ui-datepicker-prev-hover"), -1 !== this.className.indexOf("ui-datepicker-next") && e(this).addClass("ui-datepicker-next-hover"));
  }function r(t, i) {
    e.extend(t, i);for (var s in i) null == i[s] && (t[s] = i[s]);return t;
  }e.ui = e.ui || {}, e.extend(e.ui, { version: "1.11.4", keyCode: { BACKSPACE: 8, COMMA: 188, DELETE: 46, DOWN: 40, END: 35, ENTER: 13, ESCAPE: 27, HOME: 36, LEFT: 37, PAGE_DOWN: 34, PAGE_UP: 33, PERIOD: 190, RIGHT: 39, SPACE: 32, TAB: 9, UP: 38 } }), e.fn.extend({ scrollParent: function scrollParent(t) {
      var i = this.css("position"),
          s = "absolute" === i,
          n = t ? /(auto|scroll|hidden)/ : /(auto|scroll)/,
          a = this.parents().filter(function () {
        var t = e(this);return s && "static" === t.css("position") ? !1 : n.test(t.css("overflow") + t.css("overflow-y") + t.css("overflow-x"));
      }).eq(0);return "fixed" !== i && a.length ? a : e(this[0].ownerDocument || document);
    }, uniqueId: (function () {
      var e = 0;return function () {
        return this.each(function () {
          this.id || (this.id = "ui-id-" + ++e);
        });
      };
    })(), removeUniqueId: function removeUniqueId() {
      return this.each(function () {
        /^ui-id-\d+$/.test(this.id) && e(this).removeAttr("id");
      });
    } }), e.extend(e.expr[":"], { data: e.expr.createPseudo ? e.expr.createPseudo(function (t) {
      return function (i) {
        return !!e.data(i, t);
      };
    }) : function (t, i, s) {
      return !!e.data(t, s[3]);
    }, focusable: function focusable(i) {
      return t(i, !isNaN(e.attr(i, "tabindex")));
    }, tabbable: function tabbable(i) {
      var s = e.attr(i, "tabindex"),
          n = isNaN(s);return (n || s >= 0) && t(i, !n);
    } }), e("<a>").outerWidth(1).jquery || e.each(["Width", "Height"], function (t, i) {
    function s(t, i, s, a) {
      return (e.each(n, function () {
        i -= parseFloat(e.css(t, "padding" + this)) || 0, s && (i -= parseFloat(e.css(t, "border" + this + "Width")) || 0), a && (i -= parseFloat(e.css(t, "margin" + this)) || 0);
      }), i);
    }var n = "Width" === i ? ["Left", "Right"] : ["Top", "Bottom"],
        a = i.toLowerCase(),
        o = { innerWidth: e.fn.innerWidth, innerHeight: e.fn.innerHeight, outerWidth: e.fn.outerWidth, outerHeight: e.fn.outerHeight };e.fn["inner" + i] = function (t) {
      return void 0 === t ? o["inner" + i].call(this) : this.each(function () {
        e(this).css(a, s(this, t) + "px");
      });
    }, e.fn["outer" + i] = function (t, n) {
      return "number" != typeof t ? o["outer" + i].call(this, t) : this.each(function () {
        e(this).css(a, s(this, t, !0, n) + "px");
      });
    };
  }), e.fn.addBack || (e.fn.addBack = function (e) {
    return this.add(null == e ? this.prevObject : this.prevObject.filter(e));
  }), e("<a>").data("a-b", "a").removeData("a-b").data("a-b") && (e.fn.removeData = (function (t) {
    return function (i) {
      return arguments.length ? t.call(this, e.camelCase(i)) : t.call(this);
    };
  })(e.fn.removeData)), e.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()), e.fn.extend({ focus: (function (t) {
      return function (i, s) {
        return "number" == typeof i ? this.each(function () {
          var t = this;setTimeout(function () {
            e(t).focus(), s && s.call(t);
          }, i);
        }) : t.apply(this, arguments);
      };
    })(e.fn.focus), disableSelection: (function () {
      var e = "onselectstart" in document.createElement("div") ? "selectstart" : "mousedown";return function () {
        return this.bind(e + ".ui-disableSelection", function (e) {
          e.preventDefault();
        });
      };
    })(), enableSelection: function enableSelection() {
      return this.unbind(".ui-disableSelection");
    }, zIndex: function zIndex(t) {
      if (void 0 !== t) return this.css("zIndex", t);if (this.length) for (var i, s, n = e(this[0]); n.length && n[0] !== document;) {
        if ((i = n.css("position"), ("absolute" === i || "relative" === i || "fixed" === i) && (s = parseInt(n.css("zIndex"), 10), !isNaN(s) && 0 !== s))) return s;n = n.parent();
      }return 0;
    } }), e.ui.plugin = { add: function add(t, i, s) {
      var n,
          a = e.ui[t].prototype;for (n in s) a.plugins[n] = a.plugins[n] || [], a.plugins[n].push([i, s[n]]);
    }, call: function call(e, t, i, s) {
      var n,
          a = e.plugins[t];if (a && (s || e.element[0].parentNode && 11 !== e.element[0].parentNode.nodeType)) for (n = 0; a.length > n; n++) e.options[a[n][0]] && a[n][1].apply(e.element, i);
    } };var h = 0,
      l = Array.prototype.slice;e.cleanData = (function (t) {
    return function (i) {
      var s, n, a;for (a = 0; null != (n = i[a]); a++) try {
        s = e._data(n, "events"), s && s.remove && e(n).triggerHandler("remove");
      } catch (o) {}t(i);
    };
  })(e.cleanData), e.widget = function (t, i, s) {
    var n,
        a,
        o,
        r,
        h = {},
        l = t.split(".")[0];return (t = t.split(".")[1], n = l + "-" + t, s || (s = i, i = e.Widget), e.expr[":"][n.toLowerCase()] = function (t) {
      return !!e.data(t, n);
    }, e[l] = e[l] || {}, a = e[l][t], o = e[l][t] = function (e, t) {
      return this._createWidget ? (arguments.length && this._createWidget(e, t), void 0) : new o(e, t);
    }, e.extend(o, a, { version: s.version, _proto: e.extend({}, s), _childConstructors: [] }), r = new i(), r.options = e.widget.extend({}, r.options), e.each(s, function (t, s) {
      return e.isFunction(s) ? (h[t] = (function () {
        var e = function e() {
          return i.prototype[t].apply(this, arguments);
        },
            n = function n(e) {
          return i.prototype[t].apply(this, e);
        };return function () {
          var t,
              i = this._super,
              a = this._superApply;return (this._super = e, this._superApply = n, t = s.apply(this, arguments), this._super = i, this._superApply = a, t);
        };
      })(), void 0) : (h[t] = s, void 0);
    }), o.prototype = e.widget.extend(r, { widgetEventPrefix: a ? r.widgetEventPrefix || t : t }, h, { constructor: o, namespace: l, widgetName: t, widgetFullName: n }), a ? (e.each(a._childConstructors, function (t, i) {
      var s = i.prototype;e.widget(s.namespace + "." + s.widgetName, o, i._proto);
    }), delete a._childConstructors) : i._childConstructors.push(o), e.widget.bridge(t, o), o);
  }, e.widget.extend = function (t) {
    for (var i, s, n = l.call(arguments, 1), a = 0, o = n.length; o > a; a++) for (i in n[a]) s = n[a][i], n[a].hasOwnProperty(i) && void 0 !== s && (t[i] = e.isPlainObject(s) ? e.isPlainObject(t[i]) ? e.widget.extend({}, t[i], s) : e.widget.extend({}, s) : s);return t;
  }, e.widget.bridge = function (t, i) {
    var s = i.prototype.widgetFullName || t;e.fn[t] = function (n) {
      var a = "string" == typeof n,
          o = l.call(arguments, 1),
          r = this;return (a ? this.each(function () {
        var i,
            a = e.data(this, s);return "instance" === n ? (r = a, !1) : a ? e.isFunction(a[n]) && "_" !== n.charAt(0) ? (i = a[n].apply(a, o), i !== a && void 0 !== i ? (r = i && i.jquery ? r.pushStack(i.get()) : i, !1) : void 0) : e.error("no such method '" + n + "' for " + t + " widget instance") : e.error("cannot call methods on " + t + " prior to initialization; " + "attempted to call method '" + n + "'");
      }) : (o.length && (n = e.widget.extend.apply(null, [n].concat(o))), this.each(function () {
        var t = e.data(this, s);t ? (t.option(n || {}), t._init && t._init()) : e.data(this, s, new i(n, this));
      })), r);
    };
  }, e.Widget = function () {}, e.Widget._childConstructors = [], e.Widget.prototype = { widgetName: "widget", widgetEventPrefix: "", defaultElement: "<div>", options: { disabled: !1, create: null }, _createWidget: function _createWidget(t, i) {
      i = e(i || this.defaultElement || this)[0], this.element = e(i), this.uuid = h++, this.eventNamespace = "." + this.widgetName + this.uuid, this.bindings = e(), this.hoverable = e(), this.focusable = e(), i !== this && (e.data(i, this.widgetFullName, this), this._on(!0, this.element, { remove: function remove(e) {
          e.target === i && this.destroy();
        } }), this.document = e(i.style ? i.ownerDocument : i.document || i), this.window = e(this.document[0].defaultView || this.document[0].parentWindow)), this.options = e.widget.extend({}, this.options, this._getCreateOptions(), t), this._create(), this._trigger("create", null, this._getCreateEventData()), this._init();
    }, _getCreateOptions: e.noop, _getCreateEventData: e.noop, _create: e.noop, _init: e.noop, destroy: function destroy() {
      this._destroy(), this.element.unbind(this.eventNamespace).removeData(this.widgetFullName).removeData(e.camelCase(this.widgetFullName)), this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName + "-disabled " + "ui-state-disabled"), this.bindings.unbind(this.eventNamespace), this.hoverable.removeClass("ui-state-hover"), this.focusable.removeClass("ui-state-focus");
    }, _destroy: e.noop, widget: function widget() {
      return this.element;
    }, option: function option(t, i) {
      var s,
          n,
          a,
          o = t;if (0 === arguments.length) return e.widget.extend({}, this.options);if ("string" == typeof t) if ((o = {}, s = t.split("."), t = s.shift(), s.length)) {
        for (n = o[t] = e.widget.extend({}, this.options[t]), a = 0; s.length - 1 > a; a++) n[s[a]] = n[s[a]] || {}, n = n[s[a]];if ((t = s.pop(), 1 === arguments.length)) return void 0 === n[t] ? null : n[t];n[t] = i;
      } else {
        if (1 === arguments.length) return void 0 === this.options[t] ? null : this.options[t];o[t] = i;
      }return (this._setOptions(o), this);
    }, _setOptions: function _setOptions(e) {
      var t;for (t in e) this._setOption(t, e[t]);return this;
    }, _setOption: function _setOption(e, t) {
      return (this.options[e] = t, "disabled" === e && (this.widget().toggleClass(this.widgetFullName + "-disabled", !!t), t && (this.hoverable.removeClass("ui-state-hover"), this.focusable.removeClass("ui-state-focus"))), this);
    }, enable: function enable() {
      return this._setOptions({ disabled: !1 });
    }, disable: function disable() {
      return this._setOptions({ disabled: !0 });
    }, _on: function _on(t, i, s) {
      var n,
          a = this;"boolean" != typeof t && (s = i, i = t, t = !1), s ? (i = n = e(i), this.bindings = this.bindings.add(i)) : (s = i, i = this.element, n = this.widget()), e.each(s, function (s, o) {
        function r() {
          return t || a.options.disabled !== !0 && !e(this).hasClass("ui-state-disabled") ? ("string" == typeof o ? a[o] : o).apply(a, arguments) : void 0;
        }"string" != typeof o && (r.guid = o.guid = o.guid || r.guid || e.guid++);var h = s.match(/^([\w:-]*)\s*(.*)$/),
            l = h[1] + a.eventNamespace,
            u = h[2];u ? n.delegate(u, l, r) : i.bind(l, r);
      });
    }, _off: function _off(t, i) {
      i = (i || "").split(" ").join(this.eventNamespace + " ") + this.eventNamespace, t.unbind(i).undelegate(i), this.bindings = e(this.bindings.not(t).get()), this.focusable = e(this.focusable.not(t).get()), this.hoverable = e(this.hoverable.not(t).get());
    }, _delay: function _delay(e, t) {
      function i() {
        return ("string" == typeof e ? s[e] : e).apply(s, arguments);
      }var s = this;return setTimeout(i, t || 0);
    }, _hoverable: function _hoverable(t) {
      this.hoverable = this.hoverable.add(t), this._on(t, { mouseenter: function mouseenter(t) {
          e(t.currentTarget).addClass("ui-state-hover");
        }, mouseleave: function mouseleave(t) {
          e(t.currentTarget).removeClass("ui-state-hover");
        } });
    }, _focusable: function _focusable(t) {
      this.focusable = this.focusable.add(t), this._on(t, { focusin: function focusin(t) {
          e(t.currentTarget).addClass("ui-state-focus");
        }, focusout: function focusout(t) {
          e(t.currentTarget).removeClass("ui-state-focus");
        } });
    }, _trigger: function _trigger(t, i, s) {
      var n,
          a,
          o = this.options[t];if ((s = s || {}, i = e.Event(i), i.type = (t === this.widgetEventPrefix ? t : this.widgetEventPrefix + t).toLowerCase(), i.target = this.element[0], a = i.originalEvent)) for (n in a) n in i || (i[n] = a[n]);return (this.element.trigger(i, s), !(e.isFunction(o) && o.apply(this.element[0], [i].concat(s)) === !1 || i.isDefaultPrevented()));
    } }, e.each({ show: "fadeIn", hide: "fadeOut" }, function (t, i) {
    e.Widget.prototype["_" + t] = function (s, n, a) {
      "string" == typeof n && (n = { effect: n });var o,
          r = n ? n === !0 || "number" == typeof n ? i : n.effect || i : t;n = n || {}, "number" == typeof n && (n = { duration: n }), o = !e.isEmptyObject(n), n.complete = a, n.delay && s.delay(n.delay), o && e.effects && e.effects.effect[r] ? s[t](n) : r !== t && s[r] ? s[r](n.duration, n.easing, a) : s.queue(function (i) {
        e(this)[t](), a && a.call(s[0]), i();
      });
    };
  }), e.widget, (function () {
    function t(e, t, i) {
      return [parseFloat(e[0]) * (p.test(e[0]) ? t / 100 : 1), parseFloat(e[1]) * (p.test(e[1]) ? i / 100 : 1)];
    }function i(t, i) {
      return parseInt(e.css(t, i), 10) || 0;
    }function s(t) {
      var i = t[0];return 9 === i.nodeType ? { width: t.width(), height: t.height(), offset: { top: 0, left: 0 } } : e.isWindow(i) ? { width: t.width(), height: t.height(), offset: { top: t.scrollTop(), left: t.scrollLeft() } } : i.preventDefault ? { width: 0, height: 0, offset: { top: i.pageY, left: i.pageX } } : { width: t.outerWidth(), height: t.outerHeight(), offset: t.offset() };
    }e.ui = e.ui || {};var n,
        a,
        o = Math.max,
        r = Math.abs,
        h = Math.round,
        l = /left|center|right/,
        u = /top|center|bottom/,
        d = /[\+\-]\d+(\.[\d]+)?%?/,
        c = /^\w+/,
        p = /%$/,
        f = e.fn.position;e.position = { scrollbarWidth: function scrollbarWidth() {
        if (void 0 !== n) return n;var t,
            i,
            s = e("<div style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),
            a = s.children()[0];return (e("body").append(s), t = a.offsetWidth, s.css("overflow", "scroll"), i = a.offsetWidth, t === i && (i = s[0].clientWidth), s.remove(), n = t - i);
      }, getScrollInfo: function getScrollInfo(t) {
        var i = t.isWindow || t.isDocument ? "" : t.element.css("overflow-x"),
            s = t.isWindow || t.isDocument ? "" : t.element.css("overflow-y"),
            n = "scroll" === i || "auto" === i && t.width < t.element[0].scrollWidth,
            a = "scroll" === s || "auto" === s && t.height < t.element[0].scrollHeight;return { width: a ? e.position.scrollbarWidth() : 0, height: n ? e.position.scrollbarWidth() : 0 };
      }, getWithinInfo: function getWithinInfo(t) {
        var i = e(t || window),
            s = e.isWindow(i[0]),
            n = !!i[0] && 9 === i[0].nodeType;return { element: i, isWindow: s, isDocument: n, offset: i.offset() || { left: 0, top: 0 }, scrollLeft: i.scrollLeft(), scrollTop: i.scrollTop(), width: s || n ? i.width() : i.outerWidth(), height: s || n ? i.height() : i.outerHeight() };
      } }, e.fn.position = function (n) {
      if (!n || !n.of) return f.apply(this, arguments);n = e.extend({}, n);var p,
          m,
          g,
          v,
          y,
          b,
          _ = e(n.of),
          x = e.position.getWithinInfo(n.within),
          w = e.position.getScrollInfo(x),
          k = (n.collision || "flip").split(" "),
          T = {};return (b = s(_), _[0].preventDefault && (n.at = "left top"), m = b.width, g = b.height, v = b.offset, y = e.extend({}, v), e.each(["my", "at"], function () {
        var e,
            t,
            i = (n[this] || "").split(" ");1 === i.length && (i = l.test(i[0]) ? i.concat(["center"]) : u.test(i[0]) ? ["center"].concat(i) : ["center", "center"]), i[0] = l.test(i[0]) ? i[0] : "center", i[1] = u.test(i[1]) ? i[1] : "center", e = d.exec(i[0]), t = d.exec(i[1]), T[this] = [e ? e[0] : 0, t ? t[0] : 0], n[this] = [c.exec(i[0])[0], c.exec(i[1])[0]];
      }), 1 === k.length && (k[1] = k[0]), "right" === n.at[0] ? y.left += m : "center" === n.at[0] && (y.left += m / 2), "bottom" === n.at[1] ? y.top += g : "center" === n.at[1] && (y.top += g / 2), p = t(T.at, m, g), y.left += p[0], y.top += p[1], this.each(function () {
        var s,
            l,
            u = e(this),
            d = u.outerWidth(),
            c = u.outerHeight(),
            f = i(this, "marginLeft"),
            b = i(this, "marginTop"),
            D = d + f + i(this, "marginRight") + w.width,
            S = c + b + i(this, "marginBottom") + w.height,
            N = e.extend({}, y),
            M = t(T.my, u.outerWidth(), u.outerHeight());"right" === n.my[0] ? N.left -= d : "center" === n.my[0] && (N.left -= d / 2), "bottom" === n.my[1] ? N.top -= c : "center" === n.my[1] && (N.top -= c / 2), N.left += M[0], N.top += M[1], a || (N.left = h(N.left), N.top = h(N.top)), s = { marginLeft: f, marginTop: b }, e.each(["left", "top"], function (t, i) {
          e.ui.position[k[t]] && e.ui.position[k[t]][i](N, { targetWidth: m, targetHeight: g, elemWidth: d, elemHeight: c, collisionPosition: s, collisionWidth: D, collisionHeight: S, offset: [p[0] + M[0], p[1] + M[1]], my: n.my, at: n.at, within: x, elem: u });
        }), n.using && (l = function (e) {
          var t = v.left - N.left,
              i = t + m - d,
              s = v.top - N.top,
              a = s + g - c,
              h = { target: { element: _, left: v.left, top: v.top, width: m, height: g }, element: { element: u, left: N.left, top: N.top, width: d, height: c }, horizontal: 0 > i ? "left" : t > 0 ? "right" : "center", vertical: 0 > a ? "top" : s > 0 ? "bottom" : "middle" };d > m && m > r(t + i) && (h.horizontal = "center"), c > g && g > r(s + a) && (h.vertical = "middle"), h.important = o(r(t), r(i)) > o(r(s), r(a)) ? "horizontal" : "vertical", n.using.call(this, e, h);
        }), u.offset(e.extend(N, { using: l }));
      }));
    }, e.ui.position = { fit: { left: function left(e, t) {
          var i,
              s = t.within,
              n = s.isWindow ? s.scrollLeft : s.offset.left,
              a = s.width,
              r = e.left - t.collisionPosition.marginLeft,
              h = n - r,
              l = r + t.collisionWidth - a - n;t.collisionWidth > a ? h > 0 && 0 >= l ? (i = e.left + h + t.collisionWidth - a - n, e.left += h - i) : e.left = l > 0 && 0 >= h ? n : h > l ? n + a - t.collisionWidth : n : h > 0 ? e.left += h : l > 0 ? e.left -= l : e.left = o(e.left - r, e.left);
        }, top: function top(e, t) {
          var i,
              s = t.within,
              n = s.isWindow ? s.scrollTop : s.offset.top,
              a = t.within.height,
              r = e.top - t.collisionPosition.marginTop,
              h = n - r,
              l = r + t.collisionHeight - a - n;t.collisionHeight > a ? h > 0 && 0 >= l ? (i = e.top + h + t.collisionHeight - a - n, e.top += h - i) : e.top = l > 0 && 0 >= h ? n : h > l ? n + a - t.collisionHeight : n : h > 0 ? e.top += h : l > 0 ? e.top -= l : e.top = o(e.top - r, e.top);
        } }, flip: { left: function left(e, t) {
          var i,
              s,
              n = t.within,
              a = n.offset.left + n.scrollLeft,
              o = n.width,
              h = n.isWindow ? n.scrollLeft : n.offset.left,
              l = e.left - t.collisionPosition.marginLeft,
              u = l - h,
              d = l + t.collisionWidth - o - h,
              c = "left" === t.my[0] ? -t.elemWidth : "right" === t.my[0] ? t.elemWidth : 0,
              p = "left" === t.at[0] ? t.targetWidth : "right" === t.at[0] ? -t.targetWidth : 0,
              f = -2 * t.offset[0];0 > u ? (i = e.left + c + p + f + t.collisionWidth - o - a, (0 > i || r(u) > i) && (e.left += c + p + f)) : d > 0 && (s = e.left - t.collisionPosition.marginLeft + c + p + f - h, (s > 0 || d > r(s)) && (e.left += c + p + f));
        }, top: function top(e, t) {
          var i,
              s,
              n = t.within,
              a = n.offset.top + n.scrollTop,
              o = n.height,
              h = n.isWindow ? n.scrollTop : n.offset.top,
              l = e.top - t.collisionPosition.marginTop,
              u = l - h,
              d = l + t.collisionHeight - o - h,
              c = "top" === t.my[1],
              p = c ? -t.elemHeight : "bottom" === t.my[1] ? t.elemHeight : 0,
              f = "top" === t.at[1] ? t.targetHeight : "bottom" === t.at[1] ? -t.targetHeight : 0,
              m = -2 * t.offset[1];0 > u ? (s = e.top + p + f + m + t.collisionHeight - o - a, (0 > s || r(u) > s) && (e.top += p + f + m)) : d > 0 && (i = e.top - t.collisionPosition.marginTop + p + f + m - h, (i > 0 || d > r(i)) && (e.top += p + f + m));
        } }, flipfit: { left: function left() {
          e.ui.position.flip.left.apply(this, arguments), e.ui.position.fit.left.apply(this, arguments);
        }, top: function top() {
          e.ui.position.flip.top.apply(this, arguments), e.ui.position.fit.top.apply(this, arguments);
        } } }, (function () {
      var t,
          i,
          s,
          n,
          o,
          r = document.getElementsByTagName("body")[0],
          h = document.createElement("div");t = document.createElement(r ? "div" : "body"), s = { visibility: "hidden", width: 0, height: 0, border: 0, margin: 0, background: "none" }, r && e.extend(s, { position: "absolute", left: "-1000px", top: "-1000px" });for (o in s) t.style[o] = s[o];t.appendChild(h), i = r || document.documentElement, i.insertBefore(t, i.firstChild), h.style.cssText = "position: absolute; left: 10.7432222px;", n = e(h).offset().left, a = n > 10 && 11 > n, t.innerHTML = "", i.removeChild(t);
    })();
  })(), e.ui.position, e.widget("ui.menu", { version: "1.11.4", defaultElement: "<ul>", delay: 300, options: { icons: { submenu: "ui-icon-carat-1-e" }, items: "> *", menus: "ul", position: { my: "left-1 top", at: "right top" }, role: "menu", blur: null, focus: null, select: null }, _create: function _create() {
      this.activeMenu = this.element, this.mouseHandled = !1, this.element.uniqueId().addClass("ui-menu ui-widget ui-widget-content").toggleClass("ui-menu-icons", !!this.element.find(".ui-icon").length).attr({ role: this.options.role, tabIndex: 0 }), this.options.disabled && this.element.addClass("ui-state-disabled").attr("aria-disabled", "true"), this._on({ "mousedown .ui-menu-item": function mousedownUiMenuItem(e) {
          e.preventDefault();
        }, "click .ui-menu-item": function clickUiMenuItem(t) {
          var i = e(t.target);!this.mouseHandled && i.not(".ui-state-disabled").length && (this.select(t), t.isPropagationStopped() || (this.mouseHandled = !0), i.has(".ui-menu").length ? this.expand(t) : !this.element.is(":focus") && e(this.document[0].activeElement).closest(".ui-menu").length && (this.element.trigger("focus", [!0]), this.active && 1 === this.active.parents(".ui-menu").length && clearTimeout(this.timer)));
        }, "mouseenter .ui-menu-item": function mouseenterUiMenuItem(t) {
          if (!this.previousFilter) {
            var i = e(t.currentTarget);i.siblings(".ui-state-active").removeClass("ui-state-active"), this.focus(t, i);
          }
        }, mouseleave: "collapseAll", "mouseleave .ui-menu": "collapseAll", focus: function focus(e, t) {
          var i = this.active || this.element.find(this.options.items).eq(0);t || this.focus(e, i);
        }, blur: function blur(t) {
          this._delay(function () {
            e.contains(this.element[0], this.document[0].activeElement) || this.collapseAll(t);
          });
        }, keydown: "_keydown" }), this.refresh(), this._on(this.document, { click: function click(e) {
          this._closeOnDocumentClick(e) && this.collapseAll(e), this.mouseHandled = !1;
        } });
    }, _destroy: function _destroy() {
      this.element.removeAttr("aria-activedescendant").find(".ui-menu").addBack().removeClass("ui-menu ui-widget ui-widget-content ui-menu-icons ui-front").removeAttr("role").removeAttr("tabIndex").removeAttr("aria-labelledby").removeAttr("aria-expanded").removeAttr("aria-hidden").removeAttr("aria-disabled").removeUniqueId().show(), this.element.find(".ui-menu-item").removeClass("ui-menu-item").removeAttr("role").removeAttr("aria-disabled").removeUniqueId().removeClass("ui-state-hover").removeAttr("tabIndex").removeAttr("role").removeAttr("aria-haspopup").children().each(function () {
        var t = e(this);t.data("ui-menu-submenu-carat") && t.remove();
      }), this.element.find(".ui-menu-divider").removeClass("ui-menu-divider ui-widget-content");
    }, _keydown: function _keydown(t) {
      var i,
          s,
          n,
          a,
          o = !0;switch (t.keyCode) {case e.ui.keyCode.PAGE_UP:
          this.previousPage(t);break;case e.ui.keyCode.PAGE_DOWN:
          this.nextPage(t);break;case e.ui.keyCode.HOME:
          this._move("first", "first", t);break;case e.ui.keyCode.END:
          this._move("last", "last", t);break;case e.ui.keyCode.UP:
          this.previous(t);break;case e.ui.keyCode.DOWN:
          this.next(t);break;case e.ui.keyCode.LEFT:
          this.collapse(t);break;case e.ui.keyCode.RIGHT:
          this.active && !this.active.is(".ui-state-disabled") && this.expand(t);break;case e.ui.keyCode.ENTER:case e.ui.keyCode.SPACE:
          this._activate(t);break;case e.ui.keyCode.ESCAPE:
          this.collapse(t);break;default:
          o = !1, s = this.previousFilter || "", n = String.fromCharCode(t.keyCode), a = !1, clearTimeout(this.filterTimer), n === s ? a = !0 : n = s + n, i = this._filterMenuItems(n), i = a && -1 !== i.index(this.active.next()) ? this.active.nextAll(".ui-menu-item") : i, i.length || (n = String.fromCharCode(t.keyCode), i = this._filterMenuItems(n)), i.length ? (this.focus(t, i), this.previousFilter = n, this.filterTimer = this._delay(function () {
            delete this.previousFilter;
          }, 1e3)) : delete this.previousFilter;}o && t.preventDefault();
    }, _activate: function _activate(e) {
      this.active.is(".ui-state-disabled") || (this.active.is("[aria-haspopup='true']") ? this.expand(e) : this.select(e));
    }, refresh: function refresh() {
      var t,
          i,
          s = this,
          n = this.options.icons.submenu,
          a = this.element.find(this.options.menus);this.element.toggleClass("ui-menu-icons", !!this.element.find(".ui-icon").length), a.filter(":not(.ui-menu)").addClass("ui-menu ui-widget ui-widget-content ui-front").hide().attr({ role: this.options.role, "aria-hidden": "true", "aria-expanded": "false" }).each(function () {
        var t = e(this),
            i = t.parent(),
            s = e("<span>").addClass("ui-menu-icon ui-icon " + n).data("ui-menu-submenu-carat", !0);i.attr("aria-haspopup", "true").prepend(s), t.attr("aria-labelledby", i.attr("id"));
      }), t = a.add(this.element), i = t.find(this.options.items), i.not(".ui-menu-item").each(function () {
        var t = e(this);s._isDivider(t) && t.addClass("ui-widget-content ui-menu-divider");
      }), i.not(".ui-menu-item, .ui-menu-divider").addClass("ui-menu-item").uniqueId().attr({ tabIndex: -1, role: this._itemRole() }), i.filter(".ui-state-disabled").attr("aria-disabled", "true"), this.active && !e.contains(this.element[0], this.active[0]) && this.blur();
    }, _itemRole: function _itemRole() {
      return ({ menu: "menuitem", listbox: "option" })[this.options.role];
    }, _setOption: function _setOption(e, t) {
      "icons" === e && this.element.find(".ui-menu-icon").removeClass(this.options.icons.submenu).addClass(t.submenu), "disabled" === e && this.element.toggleClass("ui-state-disabled", !!t).attr("aria-disabled", t), this._super(e, t);
    }, focus: function focus(e, t) {
      var i, s;this.blur(e, e && "focus" === e.type), this._scrollIntoView(t), this.active = t.first(), s = this.active.addClass("ui-state-focus").removeClass("ui-state-active"), this.options.role && this.element.attr("aria-activedescendant", s.attr("id")), this.active.parent().closest(".ui-menu-item").addClass("ui-state-active"), e && "keydown" === e.type ? this._close() : this.timer = this._delay(function () {
        this._close();
      }, this.delay), i = t.children(".ui-menu"), i.length && e && /^mouse/.test(e.type) && this._startOpening(i), this.activeMenu = t.parent(), this._trigger("focus", e, { item: t });
    }, _scrollIntoView: function _scrollIntoView(t) {
      var i, s, n, a, o, r;this._hasScroll() && (i = parseFloat(e.css(this.activeMenu[0], "borderTopWidth")) || 0, s = parseFloat(e.css(this.activeMenu[0], "paddingTop")) || 0, n = t.offset().top - this.activeMenu.offset().top - i - s, a = this.activeMenu.scrollTop(), o = this.activeMenu.height(), r = t.outerHeight(), 0 > n ? this.activeMenu.scrollTop(a + n) : n + r > o && this.activeMenu.scrollTop(a + n - o + r));
    }, blur: function blur(e, t) {
      t || clearTimeout(this.timer), this.active && (this.active.removeClass("ui-state-focus"), this.active = null, this._trigger("blur", e, { item: this.active }));
    }, _startOpening: function _startOpening(e) {
      clearTimeout(this.timer), "true" === e.attr("aria-hidden") && (this.timer = this._delay(function () {
        this._close(), this._open(e);
      }, this.delay));
    }, _open: function _open(t) {
      var i = e.extend({ of: this.active }, this.options.position);clearTimeout(this.timer), this.element.find(".ui-menu").not(t.parents(".ui-menu")).hide().attr("aria-hidden", "true"), t.show().removeAttr("aria-hidden").attr("aria-expanded", "true").position(i);
    }, collapseAll: function collapseAll(t, i) {
      clearTimeout(this.timer), this.timer = this._delay(function () {
        var s = i ? this.element : e(t && t.target).closest(this.element.find(".ui-menu"));s.length || (s = this.element), this._close(s), this.blur(t), this.activeMenu = s;
      }, this.delay);
    }, _close: function _close(e) {
      e || (e = this.active ? this.active.parent() : this.element), e.find(".ui-menu").hide().attr("aria-hidden", "true").attr("aria-expanded", "false").end().find(".ui-state-active").not(".ui-state-focus").removeClass("ui-state-active");
    }, _closeOnDocumentClick: function _closeOnDocumentClick(t) {
      return !e(t.target).closest(".ui-menu").length;
    }, _isDivider: function _isDivider(e) {
      return !/[^\-\u2014\u2013\s]/.test(e.text());
    }, collapse: function collapse(e) {
      var t = this.active && this.active.parent().closest(".ui-menu-item", this.element);t && t.length && (this._close(), this.focus(e, t));
    }, expand: function expand(e) {
      var t = this.active && this.active.children(".ui-menu ").find(this.options.items).first();t && t.length && (this._open(t.parent()), this._delay(function () {
        this.focus(e, t);
      }));
    }, next: function next(e) {
      this._move("next", "first", e);
    }, previous: function previous(e) {
      this._move("prev", "last", e);
    }, isFirstItem: function isFirstItem() {
      return this.active && !this.active.prevAll(".ui-menu-item").length;
    }, isLastItem: function isLastItem() {
      return this.active && !this.active.nextAll(".ui-menu-item").length;
    }, _move: function _move(e, t, i) {
      var s;this.active && (s = "first" === e || "last" === e ? this.active["first" === e ? "prevAll" : "nextAll"](".ui-menu-item").eq(-1) : this.active[e + "All"](".ui-menu-item").eq(0)), s && s.length && this.active || (s = this.activeMenu.find(this.options.items)[t]()), this.focus(i, s);
    }, nextPage: function nextPage(t) {
      var i, s, n;return this.active ? (this.isLastItem() || (this._hasScroll() ? (s = this.active.offset().top, n = this.element.height(), this.active.nextAll(".ui-menu-item").each(function () {
        return (i = e(this), 0 > i.offset().top - s - n);
      }), this.focus(t, i)) : this.focus(t, this.activeMenu.find(this.options.items)[this.active ? "last" : "first"]())), void 0) : (this.next(t), void 0);
    }, previousPage: function previousPage(t) {
      var i, s, n;return this.active ? (this.isFirstItem() || (this._hasScroll() ? (s = this.active.offset().top, n = this.element.height(), this.active.prevAll(".ui-menu-item").each(function () {
        return (i = e(this), i.offset().top - s + n > 0);
      }), this.focus(t, i)) : this.focus(t, this.activeMenu.find(this.options.items).first())), void 0) : (this.next(t), void 0);
    }, _hasScroll: function _hasScroll() {
      return this.element.outerHeight() < this.element.prop("scrollHeight");
    }, select: function select(t) {
      this.active = this.active || e(t.target).closest(".ui-menu-item");var i = { item: this.active };this.active.has(".ui-menu").length || this.collapseAll(t, !0), this._trigger("select", t, i);
    }, _filterMenuItems: function _filterMenuItems(t) {
      var i = t.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"),
          s = RegExp("^" + i, "i");return this.activeMenu.find(this.options.items).filter(".ui-menu-item").filter(function () {
        return s.test(e.trim(e(this).text()));
      });
    } }), e.widget("ui.autocomplete", { version: "1.11.4", defaultElement: "<input>", options: { appendTo: null, autoFocus: !1, delay: 300, minLength: 1, position: { my: "left top", at: "left bottom", collision: "none" }, source: null, change: null, close: null, focus: null, open: null, response: null, search: null, select: null }, requestIndex: 0, pending: 0, _create: function _create() {
      var t,
          i,
          s,
          n = this.element[0].nodeName.toLowerCase(),
          a = "textarea" === n,
          o = "input" === n;this.isMultiLine = a ? !0 : o ? !1 : this.element.prop("isContentEditable"), this.valueMethod = this.element[a || o ? "val" : "text"], this.isNewMenu = !0, this.element.addClass("ui-autocomplete-input").attr("autocomplete", "off"), this._on(this.element, { keydown: function keydown(n) {
          if (this.element.prop("readOnly")) return (t = !0, s = !0, i = !0, void 0);t = !1, s = !1, i = !1;var a = e.ui.keyCode;switch (n.keyCode) {case a.PAGE_UP:
              t = !0, this._move("previousPage", n);break;case a.PAGE_DOWN:
              t = !0, this._move("nextPage", n);break;case a.UP:
              t = !0, this._keyEvent("previous", n);break;case a.DOWN:
              t = !0, this._keyEvent("next", n);break;case a.ENTER:
              this.menu.active && (t = !0, n.preventDefault(), this.menu.select(n));break;case a.TAB:
              this.menu.active && this.menu.select(n);break;case a.ESCAPE:
              this.menu.element.is(":visible") && (this.isMultiLine || this._value(this.term), this.close(n), n.preventDefault());break;default:
              i = !0, this._searchTimeout(n);}
        }, keypress: function keypress(s) {
          if (t) return (t = !1, (!this.isMultiLine || this.menu.element.is(":visible")) && s.preventDefault(), void 0);if (!i) {
            var n = e.ui.keyCode;switch (s.keyCode) {case n.PAGE_UP:
                this._move("previousPage", s);break;case n.PAGE_DOWN:
                this._move("nextPage", s);break;case n.UP:
                this._keyEvent("previous", s);break;case n.DOWN:
                this._keyEvent("next", s);}
          }
        }, input: function input(e) {
          return s ? (s = !1, e.preventDefault(), void 0) : (this._searchTimeout(e), void 0);
        }, focus: function focus() {
          this.selectedItem = null, this.previous = this._value();
        }, blur: function blur(e) {
          return this.cancelBlur ? (delete this.cancelBlur, void 0) : (clearTimeout(this.searching), this.close(e), this._change(e), void 0);
        } }), this._initSource(), this.menu = e("<ul>").addClass("ui-autocomplete ui-front").appendTo(this._appendTo()).menu({ role: null }).hide().menu("instance"), this._on(this.menu.element, { mousedown: function mousedown(t) {
          t.preventDefault(), this.cancelBlur = !0, this._delay(function () {
            delete this.cancelBlur;
          });var i = this.menu.element[0];e(t.target).closest(".ui-menu-item").length || this._delay(function () {
            var t = this;this.document.one("mousedown", function (s) {
              s.target === t.element[0] || s.target === i || e.contains(i, s.target) || t.close();
            });
          });
        }, menufocus: function menufocus(t, i) {
          var s, n;return this.isNewMenu && (this.isNewMenu = !1, t.originalEvent && /^mouse/.test(t.originalEvent.type)) ? (this.menu.blur(), this.document.one("mousemove", function () {
            e(t.target).trigger(t.originalEvent);
          }), void 0) : (n = i.item.data("ui-autocomplete-item"), !1 !== this._trigger("focus", t, { item: n }) && t.originalEvent && /^key/.test(t.originalEvent.type) && this._value(n.value), s = i.item.attr("aria-label") || n.value, s && e.trim(s).length && (this.liveRegion.children().hide(), e("<div>").text(s).appendTo(this.liveRegion)), void 0);
        }, menuselect: function menuselect(e, t) {
          var i = t.item.data("ui-autocomplete-item"),
              s = this.previous;this.element[0] !== this.document[0].activeElement && (this.element.focus(), this.previous = s, this._delay(function () {
            this.previous = s, this.selectedItem = i;
          })), !1 !== this._trigger("select", e, { item: i }) && this._value(i.value), this.term = this._value(), this.close(e), this.selectedItem = i;
        } }), this.liveRegion = e("<span>", { role: "status", "aria-live": "assertive", "aria-relevant": "additions" }).addClass("ui-helper-hidden-accessible").appendTo(this.document[0].body), this._on(this.window, { beforeunload: function beforeunload() {
          this.element.removeAttr("autocomplete");
        } });
    }, _destroy: function _destroy() {
      clearTimeout(this.searching), this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete"), this.menu.element.remove(), this.liveRegion.remove();
    }, _setOption: function _setOption(e, t) {
      this._super(e, t), "source" === e && this._initSource(), "appendTo" === e && this.menu.element.appendTo(this._appendTo()), "disabled" === e && t && this.xhr && this.xhr.abort();
    }, _appendTo: function _appendTo() {
      var t = this.options.appendTo;return (t && (t = t.jquery || t.nodeType ? e(t) : this.document.find(t).eq(0)), t && t[0] || (t = this.element.closest(".ui-front")), t.length || (t = this.document[0].body), t);
    }, _initSource: function _initSource() {
      var t,
          i,
          s = this;e.isArray(this.options.source) ? (t = this.options.source, this.source = function (i, s) {
        s(e.ui.autocomplete.filter(t, i.term));
      }) : "string" == typeof this.options.source ? (i = this.options.source, this.source = function (t, n) {
        s.xhr && s.xhr.abort(), s.xhr = e.ajax({ url: i, data: t, dataType: "json", success: function success(e) {
            n(e);
          }, error: function error() {
            n([]);
          } });
      }) : this.source = this.options.source;
    }, _searchTimeout: function _searchTimeout(e) {
      clearTimeout(this.searching), this.searching = this._delay(function () {
        var t = this.term === this._value(),
            i = this.menu.element.is(":visible"),
            s = e.altKey || e.ctrlKey || e.metaKey || e.shiftKey;(!t || t && !i && !s) && (this.selectedItem = null, this.search(null, e));
      }, this.options.delay);
    }, search: function search(e, t) {
      return (e = null != e ? e : this._value(), this.term = this._value(), e.length < this.options.minLength ? this.close(t) : this._trigger("search", t) !== !1 ? this._search(e) : void 0);
    }, _search: function _search(e) {
      this.pending++, this.element.addClass("ui-autocomplete-loading"), this.cancelSearch = !1, this.source({ term: e }, this._response());
    }, _response: function _response() {
      var t = ++this.requestIndex;return e.proxy(function (e) {
        t === this.requestIndex && this.__response(e), this.pending--, this.pending || this.element.removeClass("ui-autocomplete-loading");
      }, this);
    }, __response: function __response(e) {
      e && (e = this._normalize(e)), this._trigger("response", null, { content: e }), !this.options.disabled && e && e.length && !this.cancelSearch ? (this._suggest(e), this._trigger("open")) : this._close();
    }, close: function close(e) {
      this.cancelSearch = !0, this._close(e);
    }, _close: function _close(e) {
      this.menu.element.is(":visible") && (this.menu.element.hide(), this.menu.blur(), this.isNewMenu = !0, this._trigger("close", e));
    }, _change: function _change(e) {
      this.previous !== this._value() && this._trigger("change", e, { item: this.selectedItem });
    }, _normalize: function _normalize(t) {
      return t.length && t[0].label && t[0].value ? t : e.map(t, function (t) {
        return "string" == typeof t ? { label: t, value: t } : e.extend({}, t, { label: t.label || t.value, value: t.value || t.label });
      });
    }, _suggest: function _suggest(t) {
      var i = this.menu.element.empty();this._renderMenu(i, t), this.isNewMenu = !0, this.menu.refresh(), i.show(), this._resizeMenu(), i.position(e.extend({ of: this.element }, this.options.position)), this.options.autoFocus && this.menu.next();
    }, _resizeMenu: function _resizeMenu() {
      var e = this.menu.element;e.outerWidth(Math.max(e.width("").outerWidth() + 1, this.element.outerWidth()));
    }, _renderMenu: function _renderMenu(t, i) {
      var s = this;e.each(i, function (e, i) {
        s._renderItemData(t, i);
      });
    }, _renderItemData: function _renderItemData(e, t) {
      return this._renderItem(e, t).data("ui-autocomplete-item", t);
    }, _renderItem: function _renderItem(t, i) {
      return e("<li>").text(i.label).appendTo(t);
    }, _move: function _move(e, t) {
      return this.menu.element.is(":visible") ? this.menu.isFirstItem() && /^previous/.test(e) || this.menu.isLastItem() && /^next/.test(e) ? (this.isMultiLine || this._value(this.term), this.menu.blur(), void 0) : (this.menu[e](t), void 0) : (this.search(null, t), void 0);
    }, widget: function widget() {
      return this.menu.element;
    }, _value: function _value() {
      return this.valueMethod.apply(this.element, arguments);
    }, _keyEvent: function _keyEvent(e, t) {
      (!this.isMultiLine || this.menu.element.is(":visible")) && (this._move(e, t), t.preventDefault());
    } }), e.extend(e.ui.autocomplete, { escapeRegex: function escapeRegex(e) {
      return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
    }, filter: function filter(t, i) {
      var s = RegExp(e.ui.autocomplete.escapeRegex(i), "i");return e.grep(t, function (e) {
        return s.test(e.label || e.value || e);
      });
    } }), e.widget("ui.autocomplete", e.ui.autocomplete, { options: { messages: { noResults: "No search results.", results: function results(e) {
          return e + (e > 1 ? " results are" : " result is") + " available, use up and down arrow keys to navigate.";
        } } }, __response: function __response(t) {
      var i;this._superApply(arguments), this.options.disabled || this.cancelSearch || (i = t && t.length ? this.options.messages.results(t.length) : this.options.messages.noResults, this.liveRegion.children().hide(), e("<div>").text(i).appendTo(this.liveRegion));
    } }), e.ui.autocomplete, e.extend(e.ui, { datepicker: { version: "1.11.4" } });var u;e.extend(n.prototype, { markerClassName: "hasDatepicker", maxRows: 4, _widgetDatepicker: function _widgetDatepicker() {
      return this.dpDiv;
    }, setDefaults: function setDefaults(e) {
      return (r(this._defaults, e || {}), this);
    }, _attachDatepicker: function _attachDatepicker(t, i) {
      var s, n, a;s = t.nodeName.toLowerCase(), n = "div" === s || "span" === s, t.id || (this.uuid += 1, t.id = "dp" + this.uuid), a = this._newInst(e(t), n), a.settings = e.extend({}, i || {}), "input" === s ? this._connectDatepicker(t, a) : n && this._inlineDatepicker(t, a);
    }, _newInst: function _newInst(t, i) {
      var s = t[0].id.replace(/([^A-Za-z0-9_\-])/g, "\\\\$1");return { id: s, input: t, selectedDay: 0, selectedMonth: 0, selectedYear: 0, drawMonth: 0, drawYear: 0, inline: i, dpDiv: i ? a(e("<div class='" + this._inlineClass + " ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>")) : this.dpDiv };
    }, _connectDatepicker: function _connectDatepicker(t, i) {
      var s = e(t);i.append = e([]), i.trigger = e([]), s.hasClass(this.markerClassName) || (this._attachments(s, i), s.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp), this._autoSize(i), e.data(t, "datepicker", i), i.settings.disabled && this._disableDatepicker(t));
    }, _attachments: function _attachments(t, i) {
      var s,
          n,
          a,
          o = this._get(i, "appendText"),
          r = this._get(i, "isRTL");i.append && i.append.remove(), o && (i.append = e("<span class='" + this._appendClass + "'>" + o + "</span>"), t[r ? "before" : "after"](i.append)), t.unbind("focus", this._showDatepicker), i.trigger && i.trigger.remove(), s = this._get(i, "showOn"), ("focus" === s || "both" === s) && t.focus(this._showDatepicker), ("button" === s || "both" === s) && (n = this._get(i, "buttonText"), a = this._get(i, "buttonImage"), i.trigger = e(this._get(i, "buttonImageOnly") ? e("<img/>").addClass(this._triggerClass).attr({ src: a, alt: n, title: n }) : e("<button type='button'></button>").addClass(this._triggerClass).html(a ? e("<img/>").attr({ src: a, alt: n, title: n }) : n)), t[r ? "before" : "after"](i.trigger), i.trigger.click(function () {
        return (e.datepicker._datepickerShowing && e.datepicker._lastInput === t[0] ? e.datepicker._hideDatepicker() : e.datepicker._datepickerShowing && e.datepicker._lastInput !== t[0] ? (e.datepicker._hideDatepicker(), e.datepicker._showDatepicker(t[0])) : e.datepicker._showDatepicker(t[0]), !1);
      }));
    }, _autoSize: function _autoSize(e) {
      if (this._get(e, "autoSize") && !e.inline) {
        var t,
            i,
            s,
            n,
            a = new Date(2009, 11, 20),
            o = this._get(e, "dateFormat");o.match(/[DM]/) && (t = function (e) {
          for (i = 0, s = 0, n = 0; e.length > n; n++) e[n].length > i && (i = e[n].length, s = n);return s;
        }, a.setMonth(t(this._get(e, o.match(/MM/) ? "monthNames" : "monthNamesShort"))), a.setDate(t(this._get(e, o.match(/DD/) ? "dayNames" : "dayNamesShort")) + 20 - a.getDay())), e.input.attr("size", this._formatDate(e, a).length);
      }
    }, _inlineDatepicker: function _inlineDatepicker(t, i) {
      var s = e(t);s.hasClass(this.markerClassName) || (s.addClass(this.markerClassName).append(i.dpDiv), e.data(t, "datepicker", i), this._setDate(i, this._getDefaultDate(i), !0), this._updateDatepicker(i), this._updateAlternate(i), i.settings.disabled && this._disableDatepicker(t), i.dpDiv.css("display", "block"));
    }, _dialogDatepicker: function _dialogDatepicker(t, i, s, n, a) {
      var o,
          h,
          l,
          u,
          d,
          c = this._dialogInst;return (c || (this.uuid += 1, o = "dp" + this.uuid, this._dialogInput = e("<input type='text' id='" + o + "' style='position: absolute; top: -100px; width: 0px;'/>"), this._dialogInput.keydown(this._doKeyDown), e("body").append(this._dialogInput), c = this._dialogInst = this._newInst(this._dialogInput, !1), c.settings = {}, e.data(this._dialogInput[0], "datepicker", c)), r(c.settings, n || {}), i = i && i.constructor === Date ? this._formatDate(c, i) : i, this._dialogInput.val(i), this._pos = a ? a.length ? a : [a.pageX, a.pageY] : null, this._pos || (h = document.documentElement.clientWidth, l = document.documentElement.clientHeight, u = document.documentElement.scrollLeft || document.body.scrollLeft, d = document.documentElement.scrollTop || document.body.scrollTop, this._pos = [h / 2 - 100 + u, l / 2 - 150 + d]), this._dialogInput.css("left", this._pos[0] + 20 + "px").css("top", this._pos[1] + "px"), c.settings.onSelect = s, this._inDialog = !0, this.dpDiv.addClass(this._dialogClass), this._showDatepicker(this._dialogInput[0]), e.blockUI && e.blockUI(this.dpDiv), e.data(this._dialogInput[0], "datepicker", c), this);
    }, _destroyDatepicker: function _destroyDatepicker(t) {
      var i,
          s = e(t),
          n = e.data(t, "datepicker");s.hasClass(this.markerClassName) && (i = t.nodeName.toLowerCase(), e.removeData(t, "datepicker"), "input" === i ? (n.append.remove(), n.trigger.remove(), s.removeClass(this.markerClassName).unbind("focus", this._showDatepicker).unbind("keydown", this._doKeyDown).unbind("keypress", this._doKeyPress).unbind("keyup", this._doKeyUp)) : ("div" === i || "span" === i) && s.removeClass(this.markerClassName).empty(), u === n && (u = null));
    }, _enableDatepicker: function _enableDatepicker(t) {
      var i,
          s,
          n = e(t),
          a = e.data(t, "datepicker");n.hasClass(this.markerClassName) && (i = t.nodeName.toLowerCase(), "input" === i ? (t.disabled = !1, a.trigger.filter("button").each(function () {
        this.disabled = !1;
      }).end().filter("img").css({ opacity: "1.0", cursor: "" })) : ("div" === i || "span" === i) && (s = n.children("." + this._inlineClass), s.children().removeClass("ui-state-disabled"), s.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled", !1)), this._disabledInputs = e.map(this._disabledInputs, function (e) {
        return e === t ? null : e;
      }));
    }, _disableDatepicker: function _disableDatepicker(t) {
      var i,
          s,
          n = e(t),
          a = e.data(t, "datepicker");n.hasClass(this.markerClassName) && (i = t.nodeName.toLowerCase(), "input" === i ? (t.disabled = !0, a.trigger.filter("button").each(function () {
        this.disabled = !0;
      }).end().filter("img").css({ opacity: "0.5", cursor: "default" })) : ("div" === i || "span" === i) && (s = n.children("." + this._inlineClass), s.children().addClass("ui-state-disabled"), s.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled", !0)), this._disabledInputs = e.map(this._disabledInputs, function (e) {
        return e === t ? null : e;
      }), this._disabledInputs[this._disabledInputs.length] = t);
    }, _isDisabledDatepicker: function _isDisabledDatepicker(e) {
      if (!e) return !1;for (var t = 0; this._disabledInputs.length > t; t++) if (this._disabledInputs[t] === e) return !0;return !1;
    }, _getInst: function _getInst(t) {
      try {
        return e.data(t, "datepicker");
      } catch (i) {
        throw "Missing instance data for this datepicker";
      }
    }, _optionDatepicker: function _optionDatepicker(t, i, s) {
      var n,
          a,
          o,
          h,
          l = this._getInst(t);return 2 === arguments.length && "string" == typeof i ? "defaults" === i ? e.extend({}, e.datepicker._defaults) : l ? "all" === i ? e.extend({}, l.settings) : this._get(l, i) : null : (n = i || {}, "string" == typeof i && (n = {}, n[i] = s), l && (this._curInst === l && this._hideDatepicker(), a = this._getDateDatepicker(t, !0), o = this._getMinMaxDate(l, "min"), h = this._getMinMaxDate(l, "max"), r(l.settings, n), null !== o && void 0 !== n.dateFormat && void 0 === n.minDate && (l.settings.minDate = this._formatDate(l, o)), null !== h && void 0 !== n.dateFormat && void 0 === n.maxDate && (l.settings.maxDate = this._formatDate(l, h)), "disabled" in n && (n.disabled ? this._disableDatepicker(t) : this._enableDatepicker(t)), this._attachments(e(t), l), this._autoSize(l), this._setDate(l, a), this._updateAlternate(l), this._updateDatepicker(l)), void 0);
    }, _changeDatepicker: function _changeDatepicker(e, t, i) {
      this._optionDatepicker(e, t, i);
    }, _refreshDatepicker: function _refreshDatepicker(e) {
      var t = this._getInst(e);t && this._updateDatepicker(t);
    }, _setDateDatepicker: function _setDateDatepicker(e, t) {
      var i = this._getInst(e);i && (this._setDate(i, t), this._updateDatepicker(i), this._updateAlternate(i));
    }, _getDateDatepicker: function _getDateDatepicker(e, t) {
      var i = this._getInst(e);return (i && !i.inline && this._setDateFromField(i, t), i ? this._getDate(i) : null);
    }, _doKeyDown: function _doKeyDown(t) {
      var i,
          s,
          n,
          a = e.datepicker._getInst(t.target),
          o = !0,
          r = a.dpDiv.is(".ui-datepicker-rtl");if ((a._keyEvent = !0, e.datepicker._datepickerShowing)) switch (t.keyCode) {case 9:
          e.datepicker._hideDatepicker(), o = !1;break;case 13:
          return (n = e("td." + e.datepicker._dayOverClass + ":not(." + e.datepicker._currentClass + ")", a.dpDiv), n[0] && e.datepicker._selectDay(t.target, a.selectedMonth, a.selectedYear, n[0]), i = e.datepicker._get(a, "onSelect"), i ? (s = e.datepicker._formatDate(a), i.apply(a.input ? a.input[0] : null, [s, a])) : e.datepicker._hideDatepicker(), !1);case 27:
          e.datepicker._hideDatepicker();break;case 33:
          e.datepicker._adjustDate(t.target, t.ctrlKey ? -e.datepicker._get(a, "stepBigMonths") : -e.datepicker._get(a, "stepMonths"), "M");break;case 34:
          e.datepicker._adjustDate(t.target, t.ctrlKey ? +e.datepicker._get(a, "stepBigMonths") : +e.datepicker._get(a, "stepMonths"), "M");break;case 35:
          (t.ctrlKey || t.metaKey) && e.datepicker._clearDate(t.target), o = t.ctrlKey || t.metaKey;break;case 36:
          (t.ctrlKey || t.metaKey) && e.datepicker._gotoToday(t.target), o = t.ctrlKey || t.metaKey;break;case 37:
          (t.ctrlKey || t.metaKey) && e.datepicker._adjustDate(t.target, r ? 1 : -1, "D"), o = t.ctrlKey || t.metaKey, t.originalEvent.altKey && e.datepicker._adjustDate(t.target, t.ctrlKey ? -e.datepicker._get(a, "stepBigMonths") : -e.datepicker._get(a, "stepMonths"), "M");break;case 38:
          (t.ctrlKey || t.metaKey) && e.datepicker._adjustDate(t.target, -7, "D"), o = t.ctrlKey || t.metaKey;break;case 39:
          (t.ctrlKey || t.metaKey) && e.datepicker._adjustDate(t.target, r ? -1 : 1, "D"), o = t.ctrlKey || t.metaKey, t.originalEvent.altKey && e.datepicker._adjustDate(t.target, t.ctrlKey ? +e.datepicker._get(a, "stepBigMonths") : +e.datepicker._get(a, "stepMonths"), "M");break;case 40:
          (t.ctrlKey || t.metaKey) && e.datepicker._adjustDate(t.target, 7, "D"), o = t.ctrlKey || t.metaKey;break;default:
          o = !1;} else 36 === t.keyCode && t.ctrlKey ? e.datepicker._showDatepicker(this) : o = !1;o && (t.preventDefault(), t.stopPropagation());
    }, _doKeyPress: function _doKeyPress(t) {
      var i,
          s,
          n = e.datepicker._getInst(t.target);return e.datepicker._get(n, "constrainInput") ? (i = e.datepicker._possibleChars(e.datepicker._get(n, "dateFormat")), s = String.fromCharCode(null == t.charCode ? t.keyCode : t.charCode), t.ctrlKey || t.metaKey || " " > s || !i || i.indexOf(s) > -1) : void 0;
    }, _doKeyUp: function _doKeyUp(t) {
      var i,
          s = e.datepicker._getInst(t.target);if (s.input.val() !== s.lastVal) try {
        i = e.datepicker.parseDate(e.datepicker._get(s, "dateFormat"), s.input ? s.input.val() : null, e.datepicker._getFormatConfig(s)), i && (e.datepicker._setDateFromField(s), e.datepicker._updateAlternate(s), e.datepicker._updateDatepicker(s));
      } catch (n) {}return !0;
    }, _showDatepicker: function _showDatepicker(t) {
      if ((t = t.target || t, "input" !== t.nodeName.toLowerCase() && (t = e("input", t.parentNode)[0]), !e.datepicker._isDisabledDatepicker(t) && e.datepicker._lastInput !== t)) {
        var i, n, a, o, h, l, u;i = e.datepicker._getInst(t), e.datepicker._curInst && e.datepicker._curInst !== i && (e.datepicker._curInst.dpDiv.stop(!0, !0), i && e.datepicker._datepickerShowing && e.datepicker._hideDatepicker(e.datepicker._curInst.input[0])), n = e.datepicker._get(i, "beforeShow"), a = n ? n.apply(t, [t, i]) : {}, a !== !1 && (r(i.settings, a), i.lastVal = null, e.datepicker._lastInput = t, e.datepicker._setDateFromField(i), e.datepicker._inDialog && (t.value = ""), e.datepicker._pos || (e.datepicker._pos = e.datepicker._findPos(t), e.datepicker._pos[1] += t.offsetHeight), o = !1, e(t).parents().each(function () {
          return (o |= "fixed" === e(this).css("position"), !o);
        }), h = { left: e.datepicker._pos[0], top: e.datepicker._pos[1] }, e.datepicker._pos = null, i.dpDiv.empty(), i.dpDiv.css({ position: "absolute", display: "block", top: "-1000px" }), e.datepicker._updateDatepicker(i), h = e.datepicker._checkOffset(i, h, o), i.dpDiv.css({ position: e.datepicker._inDialog && e.blockUI ? "static" : o ? "fixed" : "absolute", display: "none", left: h.left + "px", top: h.top + "px" }), i.inline || (l = e.datepicker._get(i, "showAnim"), u = e.datepicker._get(i, "duration"), i.dpDiv.css("z-index", s(e(t)) + 1), e.datepicker._datepickerShowing = !0, e.effects && e.effects.effect[l] ? i.dpDiv.show(l, e.datepicker._get(i, "showOptions"), u) : i.dpDiv[l || "show"](l ? u : null), e.datepicker._shouldFocusInput(i) && i.input.focus(), e.datepicker._curInst = i));
      }
    }, _updateDatepicker: function _updateDatepicker(t) {
      this.maxRows = 4, u = t, t.dpDiv.empty().append(this._generateHTML(t)), this._attachHandlers(t);var i,
          s = this._getNumberOfMonths(t),
          n = s[1],
          a = 17,
          r = t.dpDiv.find("." + this._dayOverClass + " a");r.length > 0 && o.apply(r.get(0)), t.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width(""), n > 1 && t.dpDiv.addClass("ui-datepicker-multi-" + n).css("width", a * n + "em"), t.dpDiv[(1 !== s[0] || 1 !== s[1] ? "add" : "remove") + "Class"]("ui-datepicker-multi"), t.dpDiv[(this._get(t, "isRTL") ? "add" : "remove") + "Class"]("ui-datepicker-rtl"), t === e.datepicker._curInst && e.datepicker._datepickerShowing && e.datepicker._shouldFocusInput(t) && t.input.focus(), t.yearshtml && (i = t.yearshtml, setTimeout(function () {
        i === t.yearshtml && t.yearshtml && t.dpDiv.find("select.ui-datepicker-year:first").replaceWith(t.yearshtml), i = t.yearshtml = null;
      }, 0));
    }, _shouldFocusInput: function _shouldFocusInput(e) {
      return e.input && e.input.is(":visible") && !e.input.is(":disabled") && !e.input.is(":focus");
    }, _checkOffset: function _checkOffset(t, i, s) {
      var n = t.dpDiv.outerWidth(),
          a = t.dpDiv.outerHeight(),
          o = t.input ? t.input.outerWidth() : 0,
          r = t.input ? t.input.outerHeight() : 0,
          h = document.documentElement.clientWidth + (s ? 0 : e(document).scrollLeft()),
          l = document.documentElement.clientHeight + (s ? 0 : e(document).scrollTop());return (i.left -= this._get(t, "isRTL") ? n - o : 0, i.left -= s && i.left === t.input.offset().left ? e(document).scrollLeft() : 0, i.top -= s && i.top === t.input.offset().top + r ? e(document).scrollTop() : 0, i.left -= Math.min(i.left, i.left + n > h && h > n ? Math.abs(i.left + n - h) : 0), i.top -= Math.min(i.top, i.top + a > l && l > a ? Math.abs(a + r) : 0), i);
    }, _findPos: function _findPos(t) {
      for (var i, s = this._getInst(t), n = this._get(s, "isRTL"); t && ("hidden" === t.type || 1 !== t.nodeType || e.expr.filters.hidden(t));) t = t[n ? "previousSibling" : "nextSibling"];return (i = e(t).offset(), [i.left, i.top]);
    }, _hideDatepicker: function _hideDatepicker(t) {
      var i,
          s,
          n,
          a,
          o = this._curInst;!o || t && o !== e.data(t, "datepicker") || this._datepickerShowing && (i = this._get(o, "showAnim"), s = this._get(o, "duration"), n = function () {
        e.datepicker._tidyDialog(o);
      }, e.effects && (e.effects.effect[i] || e.effects[i]) ? o.dpDiv.hide(i, e.datepicker._get(o, "showOptions"), s, n) : o.dpDiv["slideDown" === i ? "slideUp" : "fadeIn" === i ? "fadeOut" : "hide"](i ? s : null, n), i || n(), this._datepickerShowing = !1, a = this._get(o, "onClose"), a && a.apply(o.input ? o.input[0] : null, [o.input ? o.input.val() : "", o]), this._lastInput = null, this._inDialog && (this._dialogInput.css({ position: "absolute", left: "0", top: "-100px" }), e.blockUI && (e.unblockUI(), e("body").append(this.dpDiv))), this._inDialog = !1);
    }, _tidyDialog: function _tidyDialog(e) {
      e.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar");
    }, _checkExternalClick: function _checkExternalClick(t) {
      if (e.datepicker._curInst) {
        var i = e(t.target),
            s = e.datepicker._getInst(i[0]);(i[0].id !== e.datepicker._mainDivId && 0 === i.parents("#" + e.datepicker._mainDivId).length && !i.hasClass(e.datepicker.markerClassName) && !i.closest("." + e.datepicker._triggerClass).length && e.datepicker._datepickerShowing && (!e.datepicker._inDialog || !e.blockUI) || i.hasClass(e.datepicker.markerClassName) && e.datepicker._curInst !== s) && e.datepicker._hideDatepicker();
      }
    }, _adjustDate: function _adjustDate(t, i, s) {
      var n = e(t),
          a = this._getInst(n[0]);this._isDisabledDatepicker(n[0]) || (this._adjustInstDate(a, i + ("M" === s ? this._get(a, "showCurrentAtPos") : 0), s), this._updateDatepicker(a));
    }, _gotoToday: function _gotoToday(t) {
      var i,
          s = e(t),
          n = this._getInst(s[0]);this._get(n, "gotoCurrent") && n.currentDay ? (n.selectedDay = n.currentDay, n.drawMonth = n.selectedMonth = n.currentMonth, n.drawYear = n.selectedYear = n.currentYear) : (i = new Date(), n.selectedDay = i.getDate(), n.drawMonth = n.selectedMonth = i.getMonth(), n.drawYear = n.selectedYear = i.getFullYear()), this._notifyChange(n), this._adjustDate(s);
    }, _selectMonthYear: function _selectMonthYear(t, i, s) {
      var n = e(t),
          a = this._getInst(n[0]);a["selected" + ("M" === s ? "Month" : "Year")] = a["draw" + ("M" === s ? "Month" : "Year")] = parseInt(i.options[i.selectedIndex].value, 10), this._notifyChange(a), this._adjustDate(n);
    }, _selectDay: function _selectDay(t, i, s, n) {
      var a,
          o = e(t);e(n).hasClass(this._unselectableClass) || this._isDisabledDatepicker(o[0]) || (a = this._getInst(o[0]), a.selectedDay = a.currentDay = e("a", n).html(), a.selectedMonth = a.currentMonth = i, a.selectedYear = a.currentYear = s, this._selectDate(t, this._formatDate(a, a.currentDay, a.currentMonth, a.currentYear)));
    }, _clearDate: function _clearDate(t) {
      var i = e(t);this._selectDate(i, "");
    }, _selectDate: function _selectDate(t, i) {
      var s,
          n = e(t),
          a = this._getInst(n[0]);i = null != i ? i : this._formatDate(a), a.input && a.input.val(i), this._updateAlternate(a), s = this._get(a, "onSelect"), s ? s.apply(a.input ? a.input[0] : null, [i, a]) : a.input && a.input.trigger("change"), a.inline ? this._updateDatepicker(a) : (this._hideDatepicker(), this._lastInput = a.input[0], "object" != typeof a.input[0] && a.input.focus(), this._lastInput = null);
    }, _updateAlternate: function _updateAlternate(t) {
      var i,
          s,
          n,
          a = this._get(t, "altField");a && (i = this._get(t, "altFormat") || this._get(t, "dateFormat"), s = this._getDate(t), n = this.formatDate(i, s, this._getFormatConfig(t)), e(a).each(function () {
        e(this).val(n);
      }));
    }, noWeekends: function noWeekends(e) {
      var t = e.getDay();return [t > 0 && 6 > t, ""];
    }, iso8601Week: function iso8601Week(e) {
      var t,
          i = new Date(e.getTime());return (i.setDate(i.getDate() + 4 - (i.getDay() || 7)), t = i.getTime(), i.setMonth(0), i.setDate(1), Math.floor(Math.round((t - i) / 864e5) / 7) + 1);
    }, parseDate: function parseDate(t, i, s) {
      if (null == t || null == i) throw "Invalid arguments";if ((i = "object" == typeof i ? "" + i : i + "", "" === i)) return null;var n,
          a,
          o,
          r,
          h = 0,
          l = (s ? s.shortYearCutoff : null) || this._defaults.shortYearCutoff,
          u = "string" != typeof l ? l : new Date().getFullYear() % 100 + parseInt(l, 10),
          d = (s ? s.dayNamesShort : null) || this._defaults.dayNamesShort,
          c = (s ? s.dayNames : null) || this._defaults.dayNames,
          p = (s ? s.monthNamesShort : null) || this._defaults.monthNamesShort,
          f = (s ? s.monthNames : null) || this._defaults.monthNames,
          m = -1,
          g = -1,
          v = -1,
          y = -1,
          b = !1,
          _ = function _(e) {
        var i = t.length > n + 1 && t.charAt(n + 1) === e;return (i && n++, i);
      },
          x = function x(e) {
        var t = _(e),
            s = "@" === e ? 14 : "!" === e ? 20 : "y" === e && t ? 4 : "o" === e ? 3 : 2,
            n = "y" === e ? s : 1,
            a = RegExp("^\\d{" + n + "," + s + "}"),
            o = i.substring(h).match(a);if (!o) throw "Missing number at position " + h;return (h += o[0].length, parseInt(o[0], 10));
      },
          w = function w(t, s, n) {
        var a = -1,
            o = e.map(_(t) ? n : s, function (e, t) {
          return [[t, e]];
        }).sort(function (e, t) {
          return -(e[1].length - t[1].length);
        });if ((e.each(o, function (e, t) {
          var s = t[1];return i.substr(h, s.length).toLowerCase() === s.toLowerCase() ? (a = t[0], h += s.length, !1) : void 0;
        }), -1 !== a)) return a + 1;throw "Unknown name at position " + h;
      },
          k = function k() {
        if (i.charAt(h) !== t.charAt(n)) throw "Unexpected literal at position " + h;h++;
      };for (n = 0; t.length > n; n++) if (b) "'" !== t.charAt(n) || _("'") ? k() : b = !1;else switch (t.charAt(n)) {case "d":
          v = x("d");break;case "D":
          w("D", d, c);break;case "o":
          y = x("o");break;case "m":
          g = x("m");break;case "M":
          g = w("M", p, f);break;case "y":
          m = x("y");break;case "@":
          r = new Date(x("@")), m = r.getFullYear(), g = r.getMonth() + 1, v = r.getDate();break;case "!":
          r = new Date((x("!") - this._ticksTo1970) / 1e4), m = r.getFullYear(), g = r.getMonth() + 1, v = r.getDate();break;case "'":
          _("'") ? k() : b = !0;break;default:
          k();}if (i.length > h && (o = i.substr(h), !/^\s+/.test(o))) throw "Extra/unparsed characters found in date: " + o;if ((-1 === m ? m = new Date().getFullYear() : 100 > m && (m += new Date().getFullYear() - new Date().getFullYear() % 100 + (u >= m ? 0 : -100)), y > -1)) for (g = 1, v = y;;) {
        if ((a = this._getDaysInMonth(m, g - 1), a >= v)) break;g++, v -= a;
      }if ((r = this._daylightSavingAdjust(new Date(m, g - 1, v)), r.getFullYear() !== m || r.getMonth() + 1 !== g || r.getDate() !== v)) throw "Invalid date";return r;
    }, ATOM: "yy-mm-dd", COOKIE: "D, dd M yy", ISO_8601: "yy-mm-dd", RFC_822: "D, d M y", RFC_850: "DD, dd-M-y", RFC_1036: "D, d M y", RFC_1123: "D, d M yy", RFC_2822: "D, d M yy", RSS: "D, d M y", TICKS: "!", TIMESTAMP: "@", W3C: "yy-mm-dd", _ticksTo1970: 1e7 * 60 * 60 * 24 * (718685 + Math.floor(492.5) - Math.floor(19.7) + Math.floor(4.925)), formatDate: function formatDate(e, t, i) {
      if (!t) return "";var s,
          n = (i ? i.dayNamesShort : null) || this._defaults.dayNamesShort,
          a = (i ? i.dayNames : null) || this._defaults.dayNames,
          o = (i ? i.monthNamesShort : null) || this._defaults.monthNamesShort,
          r = (i ? i.monthNames : null) || this._defaults.monthNames,
          h = function h(t) {
        var i = e.length > s + 1 && e.charAt(s + 1) === t;return (i && s++, i);
      },
          l = function l(e, t, i) {
        var s = "" + t;if (h(e)) for (; i > s.length;) s = "0" + s;return s;
      },
          u = function u(e, t, i, s) {
        return h(e) ? s[t] : i[t];
      },
          d = "",
          c = !1;if (t) for (s = 0; e.length > s; s++) if (c) "'" !== e.charAt(s) || h("'") ? d += e.charAt(s) : c = !1;else switch (e.charAt(s)) {case "d":
          d += l("d", t.getDate(), 2);break;case "D":
          d += u("D", t.getDay(), n, a);break;case "o":
          d += l("o", Math.round((new Date(t.getFullYear(), t.getMonth(), t.getDate()).getTime() - new Date(t.getFullYear(), 0, 0).getTime()) / 864e5), 3);break;case "m":
          d += l("m", t.getMonth() + 1, 2);break;case "M":
          d += u("M", t.getMonth(), o, r);break;case "y":
          d += h("y") ? t.getFullYear() : (10 > t.getYear() % 100 ? "0" : "") + t.getYear() % 100;break;case "@":
          d += t.getTime();break;case "!":
          d += 1e4 * t.getTime() + this._ticksTo1970;break;case "'":
          h("'") ? d += "'" : c = !0;break;default:
          d += e.charAt(s);}return d;
    }, _possibleChars: function _possibleChars(e) {
      var t,
          i = "",
          s = !1,
          n = function n(i) {
        var s = e.length > t + 1 && e.charAt(t + 1) === i;return (s && t++, s);
      };for (t = 0; e.length > t; t++) if (s) "'" !== e.charAt(t) || n("'") ? i += e.charAt(t) : s = !1;else switch (e.charAt(t)) {case "d":case "m":case "y":case "@":
          i += "0123456789";break;case "D":case "M":
          return null;case "'":
          n("'") ? i += "'" : s = !0;break;default:
          i += e.charAt(t);}return i;
    }, _get: function _get(e, t) {
      return void 0 !== e.settings[t] ? e.settings[t] : this._defaults[t];
    }, _setDateFromField: function _setDateFromField(e, t) {
      if (e.input.val() !== e.lastVal) {
        var i = this._get(e, "dateFormat"),
            s = e.lastVal = e.input ? e.input.val() : null,
            n = this._getDefaultDate(e),
            a = n,
            o = this._getFormatConfig(e);try {
          a = this.parseDate(i, s, o) || n;
        } catch (r) {
          s = t ? "" : s;
        }e.selectedDay = a.getDate(), e.drawMonth = e.selectedMonth = a.getMonth(), e.drawYear = e.selectedYear = a.getFullYear(), e.currentDay = s ? a.getDate() : 0, e.currentMonth = s ? a.getMonth() : 0, e.currentYear = s ? a.getFullYear() : 0, this._adjustInstDate(e);
      }
    }, _getDefaultDate: function _getDefaultDate(e) {
      return this._restrictMinMax(e, this._determineDate(e, this._get(e, "defaultDate"), new Date()));
    }, _determineDate: function _determineDate(t, i, s) {
      var n = function n(e) {
        var t = new Date();return (t.setDate(t.getDate() + e), t);
      },
          a = function a(i) {
        try {
          return e.datepicker.parseDate(e.datepicker._get(t, "dateFormat"), i, e.datepicker._getFormatConfig(t));
        } catch (s) {}for (var n = (i.toLowerCase().match(/^c/) ? e.datepicker._getDate(t) : null) || new Date(), a = n.getFullYear(), o = n.getMonth(), r = n.getDate(), h = /([+\-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g, l = h.exec(i); l;) {
          switch (l[2] || "d") {case "d":case "D":
              r += parseInt(l[1], 10);break;case "w":case "W":
              r += 7 * parseInt(l[1], 10);break;case "m":case "M":
              o += parseInt(l[1], 10), r = Math.min(r, e.datepicker._getDaysInMonth(a, o));break;case "y":case "Y":
              a += parseInt(l[1], 10), r = Math.min(r, e.datepicker._getDaysInMonth(a, o));}l = h.exec(i);
        }return new Date(a, o, r);
      },
          o = null == i || "" === i ? s : "string" == typeof i ? a(i) : "number" == typeof i ? isNaN(i) ? s : n(i) : new Date(i.getTime());return (o = o && "Invalid Date" == "" + o ? s : o, o && (o.setHours(0), o.setMinutes(0), o.setSeconds(0), o.setMilliseconds(0)), this._daylightSavingAdjust(o));
    }, _daylightSavingAdjust: function _daylightSavingAdjust(e) {
      return e ? (e.setHours(e.getHours() > 12 ? e.getHours() + 2 : 0), e) : null;
    }, _setDate: function _setDate(e, t, i) {
      var s = !t,
          n = e.selectedMonth,
          a = e.selectedYear,
          o = this._restrictMinMax(e, this._determineDate(e, t, new Date()));e.selectedDay = e.currentDay = o.getDate(), e.drawMonth = e.selectedMonth = e.currentMonth = o.getMonth(), e.drawYear = e.selectedYear = e.currentYear = o.getFullYear(), n === e.selectedMonth && a === e.selectedYear || i || this._notifyChange(e), this._adjustInstDate(e), e.input && e.input.val(s ? "" : this._formatDate(e));
    }, _getDate: function _getDate(e) {
      var t = !e.currentYear || e.input && "" === e.input.val() ? null : this._daylightSavingAdjust(new Date(e.currentYear, e.currentMonth, e.currentDay));return t;
    }, _attachHandlers: function _attachHandlers(t) {
      var i = this._get(t, "stepMonths"),
          s = "#" + t.id.replace(/\\\\/g, "\\");t.dpDiv.find("[data-handler]").map(function () {
        var t = { prev: function prev() {
            e.datepicker._adjustDate(s, -i, "M");
          }, next: function next() {
            e.datepicker._adjustDate(s, +i, "M");
          }, hide: function hide() {
            e.datepicker._hideDatepicker();
          }, today: function today() {
            e.datepicker._gotoToday(s);
          }, selectDay: function selectDay() {
            return (e.datepicker._selectDay(s, +this.getAttribute("data-month"), +this.getAttribute("data-year"), this), !1);
          }, selectMonth: function selectMonth() {
            return (e.datepicker._selectMonthYear(s, this, "M"), !1);
          }, selectYear: function selectYear() {
            return (e.datepicker._selectMonthYear(s, this, "Y"), !1);
          } };e(this).bind(this.getAttribute("data-event"), t[this.getAttribute("data-handler")]);
      });
    }, _generateHTML: function _generateHTML(e) {
      var t,
          i,
          s,
          n,
          a,
          o,
          r,
          h,
          l,
          u,
          d,
          c,
          p,
          f,
          m,
          g,
          v,
          y,
          b,
          _,
          x,
          w,
          k,
          T,
          D,
          S,
          N,
          M,
          C,
          P,
          A,
          I,
          z,
          H,
          F,
          E,
          W,
          O,
          L,
          j = new Date(),
          R = this._daylightSavingAdjust(new Date(j.getFullYear(), j.getMonth(), j.getDate())),
          Y = this._get(e, "isRTL"),
          J = this._get(e, "showButtonPanel"),
          B = this._get(e, "hideIfNoPrevNext"),
          K = this._get(e, "navigationAsDateFormat"),
          U = this._getNumberOfMonths(e),
          V = this._get(e, "showCurrentAtPos"),
          q = this._get(e, "stepMonths"),
          G = 1 !== U[0] || 1 !== U[1],
          X = this._daylightSavingAdjust(e.currentDay ? new Date(e.currentYear, e.currentMonth, e.currentDay) : new Date(9999, 9, 9)),
          $ = this._getMinMaxDate(e, "min"),
          Q = this._getMinMaxDate(e, "max"),
          Z = e.drawMonth - V,
          et = e.drawYear;if ((0 > Z && (Z += 12, et--), Q)) for (t = this._daylightSavingAdjust(new Date(Q.getFullYear(), Q.getMonth() - U[0] * U[1] + 1, Q.getDate())), t = $ && $ > t ? $ : t; this._daylightSavingAdjust(new Date(et, Z, 1)) > t;) Z--, 0 > Z && (Z = 11, et--);for (e.drawMonth = Z, e.drawYear = et, i = this._get(e, "prevText"), i = K ? this.formatDate(i, this._daylightSavingAdjust(new Date(et, Z - q, 1)), this._getFormatConfig(e)) : i, s = this._canAdjustMonth(e, -1, et, Z) ? "<a class='ui-datepicker-prev ui-corner-all' data-handler='prev' data-event='click' title='" + i + "'><span class='ui-icon ui-icon-circle-triangle-" + (Y ? "e" : "w") + "'>" + i + "</span></a>" : B ? "" : "<a class='ui-datepicker-prev ui-corner-all ui-state-disabled' title='" + i + "'><span class='ui-icon ui-icon-circle-triangle-" + (Y ? "e" : "w") + "'>" + i + "</span></a>", n = this._get(e, "nextText"), n = K ? this.formatDate(n, this._daylightSavingAdjust(new Date(et, Z + q, 1)), this._getFormatConfig(e)) : n, a = this._canAdjustMonth(e, 1, et, Z) ? "<a class='ui-datepicker-next ui-corner-all' data-handler='next' data-event='click' title='" + n + "'><span class='ui-icon ui-icon-circle-triangle-" + (Y ? "w" : "e") + "'>" + n + "</span></a>" : B ? "" : "<a class='ui-datepicker-next ui-corner-all ui-state-disabled' title='" + n + "'><span class='ui-icon ui-icon-circle-triangle-" + (Y ? "w" : "e") + "'>" + n + "</span></a>", o = this._get(e, "currentText"), r = this._get(e, "gotoCurrent") && e.currentDay ? X : R, o = K ? this.formatDate(o, r, this._getFormatConfig(e)) : o, h = e.inline ? "" : "<button type='button' class='ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all' data-handler='hide' data-event='click'>" + this._get(e, "closeText") + "</button>", l = J ? "<div class='ui-datepicker-buttonpane ui-widget-content'>" + (Y ? h : "") + (this._isInRange(e, r) ? "<button type='button' class='ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all' data-handler='today' data-event='click'>" + o + "</button>" : "") + (Y ? "" : h) + "</div>" : "", u = parseInt(this._get(e, "firstDay"), 10), u = isNaN(u) ? 0 : u, d = this._get(e, "showWeek"), c = this._get(e, "dayNames"), p = this._get(e, "dayNamesMin"), f = this._get(e, "monthNames"), m = this._get(e, "monthNamesShort"), g = this._get(e, "beforeShowDay"), v = this._get(e, "showOtherMonths"), y = this._get(e, "selectOtherMonths"), b = this._getDefaultDate(e), _ = "", w = 0; U[0] > w; w++) {
        for (k = "", this.maxRows = 4, T = 0; U[1] > T; T++) {
          if ((D = this._daylightSavingAdjust(new Date(et, Z, e.selectedDay)), S = " ui-corner-all", N = "", G)) {
            if ((N += "<div class='ui-datepicker-group", U[1] > 1)) switch (T) {case 0:
                N += " ui-datepicker-group-first", S = " ui-corner-" + (Y ? "right" : "left");break;case U[1] - 1:
                N += " ui-datepicker-group-last", S = " ui-corner-" + (Y ? "left" : "right");break;default:
                N += " ui-datepicker-group-middle", S = "";}N += "'>";
          }for (N += "<div class='ui-datepicker-header ui-widget-header ui-helper-clearfix" + S + "'>" + (/all|left/.test(S) && 0 === w ? Y ? a : s : "") + (/all|right/.test(S) && 0 === w ? Y ? s : a : "") + this._generateMonthYearHeader(e, Z, et, $, Q, w > 0 || T > 0, f, m) + "</div><table class='ui-datepicker-calendar'><thead>" + "<tr>", M = d ? "<th class='ui-datepicker-week-col'>" + this._get(e, "weekHeader") + "</th>" : "", x = 0; 7 > x; x++) C = (x + u) % 7, M += "<th scope='col'" + ((x + u + 6) % 7 >= 5 ? " class='ui-datepicker-week-end'" : "") + ">" + "<span title='" + c[C] + "'>" + p[C] + "</span></th>";for (N += M + "</tr></thead><tbody>", P = this._getDaysInMonth(et, Z), et === e.selectedYear && Z === e.selectedMonth && (e.selectedDay = Math.min(e.selectedDay, P)), A = (this._getFirstDayOfMonth(et, Z) - u + 7) % 7, I = Math.ceil((A + P) / 7), z = G ? this.maxRows > I ? this.maxRows : I : I, this.maxRows = z, H = this._daylightSavingAdjust(new Date(et, Z, 1 - A)), F = 0; z > F; F++) {
            for (N += "<tr>", E = d ? "<td class='ui-datepicker-week-col'>" + this._get(e, "calculateWeek")(H) + "</td>" : "", x = 0; 7 > x; x++) W = g ? g.apply(e.input ? e.input[0] : null, [H]) : [!0, ""], O = H.getMonth() !== Z, L = O && !y || !W[0] || $ && $ > H || Q && H > Q, E += "<td class='" + ((x + u + 6) % 7 >= 5 ? " ui-datepicker-week-end" : "") + (O ? " ui-datepicker-other-month" : "") + (H.getTime() === D.getTime() && Z === e.selectedMonth && e._keyEvent || b.getTime() === H.getTime() && b.getTime() === D.getTime() ? " " + this._dayOverClass : "") + (L ? " " + this._unselectableClass + " ui-state-disabled" : "") + (O && !v ? "" : " " + W[1] + (H.getTime() === X.getTime() ? " " + this._currentClass : "") + (H.getTime() === R.getTime() ? " ui-datepicker-today" : "")) + "'" + (O && !v || !W[2] ? "" : " title='" + W[2].replace(/'/g, "&#39;") + "'") + (L ? "" : " data-handler='selectDay' data-event='click' data-month='" + H.getMonth() + "' data-year='" + H.getFullYear() + "'") + ">" + (O && !v ? "&#xa0;" : L ? "<span class='ui-state-default'>" + H.getDate() + "</span>" : "<a class='ui-state-default" + (H.getTime() === R.getTime() ? " ui-state-highlight" : "") + (H.getTime() === X.getTime() ? " ui-state-active" : "") + (O ? " ui-priority-secondary" : "") + "' href='#'>" + H.getDate() + "</a>") + "</td>", H.setDate(H.getDate() + 1), H = this._daylightSavingAdjust(H);
            N += E + "</tr>";
          }Z++, Z > 11 && (Z = 0, et++), N += "</tbody></table>" + (G ? "</div>" + (U[0] > 0 && T === U[1] - 1 ? "<div class='ui-datepicker-row-break'></div>" : "") : ""), k += N;
        }_ += k;
      }return (_ += l, e._keyEvent = !1, _);
    }, _generateMonthYearHeader: function _generateMonthYearHeader(e, t, i, s, n, a, o, r) {
      var h,
          l,
          u,
          d,
          c,
          p,
          f,
          m,
          g = this._get(e, "changeMonth"),
          v = this._get(e, "changeYear"),
          y = this._get(e, "showMonthAfterYear"),
          b = "<div class='ui-datepicker-title'>",
          _ = "";if (a || !g) _ += "<span class='ui-datepicker-month'>" + o[t] + "</span>";else {
        for (h = s && s.getFullYear() === i, l = n && n.getFullYear() === i, _ += "<select class='ui-datepicker-month' data-handler='selectMonth' data-event='change'>", u = 0; 12 > u; u++) (!h || u >= s.getMonth()) && (!l || n.getMonth() >= u) && (_ += "<option value='" + u + "'" + (u === t ? " selected='selected'" : "") + ">" + r[u] + "</option>");_ += "</select>";
      }if ((y || (b += _ + (!a && g && v ? "" : "&#xa0;")), !e.yearshtml)) if ((e.yearshtml = "", a || !v)) b += "<span class='ui-datepicker-year'>" + i + "</span>";else {
        for (d = this._get(e, "yearRange").split(":"), c = new Date().getFullYear(), p = function (e) {
          var t = e.match(/c[+\-].*/) ? i + parseInt(e.substring(1), 10) : e.match(/[+\-].*/) ? c + parseInt(e, 10) : parseInt(e, 10);return isNaN(t) ? c : t;
        }, f = p(d[0]), m = Math.max(f, p(d[1] || "")), f = s ? Math.max(f, s.getFullYear()) : f, m = n ? Math.min(m, n.getFullYear()) : m, e.yearshtml += "<select class='ui-datepicker-year' data-handler='selectYear' data-event='change'>"; m >= f; f++) e.yearshtml += "<option value='" + f + "'" + (f === i ? " selected='selected'" : "") + ">" + f + "</option>";e.yearshtml += "</select>", b += e.yearshtml, e.yearshtml = null;
      }return (b += this._get(e, "yearSuffix"), y && (b += (!a && g && v ? "" : "&#xa0;") + _), b += "</div>");
    }, _adjustInstDate: function _adjustInstDate(e, t, i) {
      var s = e.drawYear + ("Y" === i ? t : 0),
          n = e.drawMonth + ("M" === i ? t : 0),
          a = Math.min(e.selectedDay, this._getDaysInMonth(s, n)) + ("D" === i ? t : 0),
          o = this._restrictMinMax(e, this._daylightSavingAdjust(new Date(s, n, a)));e.selectedDay = o.getDate(), e.drawMonth = e.selectedMonth = o.getMonth(), e.drawYear = e.selectedYear = o.getFullYear(), ("M" === i || "Y" === i) && this._notifyChange(e);
    }, _restrictMinMax: function _restrictMinMax(e, t) {
      var i = this._getMinMaxDate(e, "min"),
          s = this._getMinMaxDate(e, "max"),
          n = i && i > t ? i : t;return s && n > s ? s : n;
    }, _notifyChange: function _notifyChange(e) {
      var t = this._get(e, "onChangeMonthYear");t && t.apply(e.input ? e.input[0] : null, [e.selectedYear, e.selectedMonth + 1, e]);
    }, _getNumberOfMonths: function _getNumberOfMonths(e) {
      var t = this._get(e, "numberOfMonths");return null == t ? [1, 1] : "number" == typeof t ? [1, t] : t;
    }, _getMinMaxDate: function _getMinMaxDate(e, t) {
      return this._determineDate(e, this._get(e, t + "Date"), null);
    }, _getDaysInMonth: function _getDaysInMonth(e, t) {
      return 32 - this._daylightSavingAdjust(new Date(e, t, 32)).getDate();
    }, _getFirstDayOfMonth: function _getFirstDayOfMonth(e, t) {
      return new Date(e, t, 1).getDay();
    }, _canAdjustMonth: function _canAdjustMonth(e, t, i, s) {
      var n = this._getNumberOfMonths(e),
          a = this._daylightSavingAdjust(new Date(i, s + (0 > t ? t : n[0] * n[1]), 1));return (0 > t && a.setDate(this._getDaysInMonth(a.getFullYear(), a.getMonth())), this._isInRange(e, a));
    }, _isInRange: function _isInRange(e, t) {
      var i,
          s,
          n = this._getMinMaxDate(e, "min"),
          a = this._getMinMaxDate(e, "max"),
          o = null,
          r = null,
          h = this._get(e, "yearRange");return (h && (i = h.split(":"), s = new Date().getFullYear(), o = parseInt(i[0], 10), r = parseInt(i[1], 10), i[0].match(/[+\-].*/) && (o += s), i[1].match(/[+\-].*/) && (r += s)), (!n || t.getTime() >= n.getTime()) && (!a || t.getTime() <= a.getTime()) && (!o || t.getFullYear() >= o) && (!r || r >= t.getFullYear()));
    }, _getFormatConfig: function _getFormatConfig(e) {
      var t = this._get(e, "shortYearCutoff");return (t = "string" != typeof t ? t : new Date().getFullYear() % 100 + parseInt(t, 10), { shortYearCutoff: t, dayNamesShort: this._get(e, "dayNamesShort"), dayNames: this._get(e, "dayNames"), monthNamesShort: this._get(e, "monthNamesShort"), monthNames: this._get(e, "monthNames") });
    }, _formatDate: function _formatDate(e, t, i, s) {
      t || (e.currentDay = e.selectedDay, e.currentMonth = e.selectedMonth, e.currentYear = e.selectedYear);var n = t ? "object" == typeof t ? t : this._daylightSavingAdjust(new Date(s, i, t)) : this._daylightSavingAdjust(new Date(e.currentYear, e.currentMonth, e.currentDay));return this.formatDate(this._get(e, "dateFormat"), n, this._getFormatConfig(e));
    } }), e.fn.datepicker = function (t) {
    if (!this.length) return this;e.datepicker.initialized || (e(document).mousedown(e.datepicker._checkExternalClick), e.datepicker.initialized = !0), 0 === e("#" + e.datepicker._mainDivId).length && e("body").append(e.datepicker.dpDiv);var i = Array.prototype.slice.call(arguments, 1);return "string" != typeof t || "isDisabled" !== t && "getDate" !== t && "widget" !== t ? "option" === t && 2 === arguments.length && "string" == typeof arguments[1] ? e.datepicker["_" + t + "Datepicker"].apply(e.datepicker, [this[0]].concat(i)) : this.each(function () {
      "string" == typeof t ? e.datepicker["_" + t + "Datepicker"].apply(e.datepicker, [this].concat(i)) : e.datepicker._attachDatepicker(this, t);
    }) : e.datepicker["_" + t + "Datepicker"].apply(e.datepicker, [this[0]].concat(i));
  }, e.datepicker = new n(), e.datepicker.initialized = !1, e.datepicker.uuid = new Date().getTime(), e.datepicker.version = "1.11.4", e.datepicker;
});

},{}],9:[function(require,module,exports){
'use strict';

(function () {

  // Ignore for unsupported browsers
  if (!(window.history && window.history.pushState)) return;

  // Main function that listens for clicks to the selector and opens the
  // href of the element in the iframe modal.
  //
  // @param {String} selector DOM query selector e.g. 'ul.list-items a'

  var scrollFrame = function scrollFrame(selector) {
    refreshOnNewIframePage();
    document.addEventListener('click', function (e) {
      // Ignore ctrl/cmd/shift clicks, as well as middle clicks
      if (e.ctrlKey || e.metaKey || e.shiftKey || e.which === 2) return;

      // Ignore if the element doesnt match our selector
      var els = document.querySelectorAll(selector);
      var elMatchesSelector = (window.Array || Array). // Hack for Zombie testing
      prototype.filter.call(els, function (el) {
        return el == e.target || el.contains(e.target);
      }).length > 0;
      if (!elMatchesSelector) return;

      // Get the href & open the iframe on that url
      var href = e.target.href || e.target.parentNode.href;
      if (href) {
        e.preventDefault();
        openIframe(href);
      }
    });
  };

  // Change pushState and open the iframe modal pointing to this url.
  //
  // @param {String} url

  var openIframe = function openIframe(url) {
    var prevHref = location.href;

    // Change the history
    history.pushState({ scrollFrame: true, href: location.href }, '', url);

    // Create the wrapper & iframe modal
    var body = document.getElementsByTagName('body')[0];
    var iOS = navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false;
    var attributes = ['position: fixed', 'top: 0', 'left: 0', 'width: 100%', 'height: 100%', 'z-index: 10000000', 'background-color: white', 'border: 0'];

    //only add scrolling fix for ios devices
    if (iOS) {
      attributes.push('overflow-y: scroll');
      attributes.push('-webkit-overflow-scrolling: touch');
    }
    //create wrapper for iOS scroll fix
    var wrapper = document.createElement("div");
    wrapper.setAttribute('style', attributes.join(';'));
    var iframe = document.createElement("iframe");
    iframe.className = 'scroll-frame-iframe';
    iframe.setAttribute('style', ['width: 100%', 'height: 100%', 'position:absolute', 'border: 0'].join(';'));

    // Lock the body from scrolling & hide the body's scroll bars.
    body.setAttribute('style', 'overflow: hidden;' + (body.getAttribute('style') || ''));

    // Add a class to the body while the iframe loads then append it
    body.className += ' scroll-frame-loading';
    iframe.onload = function () {
      body.className = body.className.replace(' scroll-frame-loading', '');
    };
    wrapper.appendChild(iframe);
    body.appendChild(wrapper);
    iframe.contentWindow.location.replace(url);

    // On back-button remove the wrapper
    var onPopState = function onPopState(e) {
      if (location.href != prevHref) return;
      wrapper.removeChild(iframe);
      body.removeChild(wrapper);
      body.setAttribute('style', body.getAttribute('style').replace('overflow: hidden;', ''));
      removeEventListener('popstate', onPopState);
    };
    addEventListener('popstate', onPopState);
  };

  // To keep iframes from stacking up inside of each other and potentially
  // getting into a very messy state we'll use messaging b/t iframes to
  // signal when we've dived more than a page deep inside of our iframe modal
  // and cause the page to do a full refresh instead.

  var refreshOnNewIframePage = function refreshOnNewIframePage() {
    addEventListener('message', function (e) {
      if (!e.data.href) return;
      if (!e.data.scrollFrame == true) return;
      if (e.data.href == this.location.href) return;
      var body = document.getElementsByTagName('body')[0];
      var html = document.getElementsByTagName('html')[0];
      this.location.assign(e.data.href);
    });
  };

  // Export for CommonJS & window global
  if (typeof module != 'undefined') {
    module.exports = scrollFrame;
  } else {
    window.scrollFrame = scrollFrame;
  }
})();

},{}]},{},[1]);
