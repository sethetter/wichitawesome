window.apiUrl = 'http://'+ window.location.hostname +'/api/';

var $ = require('jquery');
var scrollawesome = require('./scrollawesome');
var scrollFrame = require('scroll-frame');

if($('#event_list').length > 0) {
    scrollawesome();
    scrollFrame('.event-name');
}

require('./form');