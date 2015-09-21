window.apiUrl = '//'+ window.location.hostname +'/api/';

var $ = require('jquery');
var scrollawesome = require('./scrollawesome');
var scrollFrame = require('scroll-frame');

if($('#event_list').length > 0) {
    scrollawesome.init();
    scrollFrame('.event-name');
}

function updateQueryString(key, value, url) {
    if (!url) url = window.location.href;
    var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
        hash;

    if (re.test(url)) {
        if (typeof value !== 'undefined' && value !== null) {
            return url.replace(re, '$1' + key + "=" + value + '$2$3');
        } else {
            hash = url.split('#');
            url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
            if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
                url += '#' + hash[1];
            return url;
        }
    } else {
        if (typeof value !== 'undefined' && value !== null) {
            var separator = url.indexOf('?') !== -1 ? '&' : '?';
            hash = url.split('#');
            url = hash[0] + separator + key + '=' + value;
            if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
                url += '#' + hash[1];
            return url;
        } else {
            return url;
        }
    }
}

var $tags = $('[data-toggle="tag"]')
$tags.change( function() {
    var values = [];
    $.each( $tags.serializeArray(), function( i, field ) {
        values.push(field.value);
    });
    window.location = values.length ? updateQueryString('tags', values.join(',')) : updateQueryString('tags');
});

$('.js-filter-btn').click(function() {
    var $this = $(this);
    var $container = $('.js-filter-container');
    if( ! $container.hasClass('js-open') ) {
        $container.slideDown(100, function(){
            $container.addClass('js-open');
            scrollawesome.refresh();
        });
        $this.text('Close');
    } else {
        $container.slideUp(100, function() {
            $container.removeClass('js-open');
            scrollawesome.refresh();
        });
        $this.text('Filter');
    }
    return false;
});

require('./form');