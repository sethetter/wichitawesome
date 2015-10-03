module.exports = (function(undefined) {
    function documentHeight() {
        return Math.max(
            document.documentElement.clientHeight,
            document.body.scrollHeight,
            document.documentElement.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.offsetHeight
        );
    }
    var scroll = window.requestAnimationFrame ||
                 window.webkitRequestAnimationFrame ||
                 window.mozRequestAnimationFrame ||
                 window.msRequestAnimationFrame ||
                 window.oRequestAnimationFrame ||
                 // IE Fallback, you can even fallback to onscroll
                 function(callback){ window.setTimeout(callback, 1000/60) };

    var startSpace = 16;
    var stopSpace = 64;
    var lastPosition = -1;
    var elements;
    var matrix = [];
    var eventList = document.getElementById('event_list');
    var pagination = document.getElementById('pagination_next');
    var paginationStart;
    var loadingPage = false;

    if (pagination) {
        var nextPage = 2; // Assume we are on page 1
        var queryStr = pagination.href.split('?')[1];
        var queryVars = queryStr.split('&');
        var i = 0;
        for ( var i; i < queryVars.length; i++ ) {
            var pair = queryVars[i].split('=');
            if (decodeURIComponent(pair[0]) == 'page') {
                nextPage = decodeURIComponent(pair[1]); // If page is set use that instead
                queryVars.splice(i, 1);
            }
        }
    }

    var loop = function(){
        var scrollY = window.pageYOffset;

        if (lastPosition == scrollY) {
            scroll(loop);
            return false;
        }
        
        lastPosition = scrollY;

        var l = matrix.length;
        var i = 0;
        for(i; i<l; i++) {       
            if (scrollY >= matrix[i].start) {        
                var stop = matrix[i+1] ? matrix[i+1].start - stopSpace - matrix[i].height : matrix[i].start;

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

            if(pagination && scrollY >= paginationStart && !loadingPage) {
                loadingPage = true;
                var url = apiUrl + 'view/events/?' + queryVars.join('&') + '&page=' + nextPage;
                var req = new XMLHttpRequest(); 
                req.open('GET', url, true);
                req.onreadystatechange = function () {
                    if(req.status !== 200) {
                        console.log('Status error: '+req.status);
                        return;
                    }
                    if(req.readyState === 4) {
                        eventList.insertAdjacentHTML('beforeend', req.responseText);
                        pagination.style.display = 'none';
                        nextPage++;
                        refresh();
                    }
                }
                req.send(null);
            }
        };
        scroll(loop)
    };

    var refresh = function() {
        // Convert NodeList into an Array so that we can delete elements
        // without jacking up the Array's indexes
        elements = [].slice.call(document.getElementsByClassName('event-date'));
        var l = elements.length;
        var i = 0; // index for elements
        var j = 0; // index for matrix
        var date = null;
        for(i; i<l; i++) {
            var datetime = elements[i].getAttribute('datetime');
            if( date === datetime.substr(0, datetime.indexOf('T')) ) {
                elements[i].parentNode.removeChild(elements[i]);
                continue;
            }

            date = datetime.substr(0, datetime.indexOf('T'));

            matrix[j] = { el: elements[i] };
            matrix[j].el.style['position'] = '';
            matrix[j].el.style['top'] = '';
            matrix[j].height = matrix[j].el.offsetHeight
            matrix[j].start = matrix[j].el.offsetTop - startSpace;
            j++;
        }
        paginationStart = documentHeight() - (window.innerHeight * 2) ;
        loadingPage = false;
    };

    window.onresize = refresh;

    var init = function() {
        refresh();
        loop();
    };

    return { 
        init: init, 
        refresh: refresh 
    }
    
})();