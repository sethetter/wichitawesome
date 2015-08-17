module.exports = function(undefined) {
    function documentHeight() {
        return Math.max(
            document.documentElement.clientHeight,
            document.body.scrollHeight,
            document.documentElement.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.offsetHeight
        );
    }
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
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
    var size;
    var matrix = [];
    var eventList = document.getElementById('event_list');
    var pagination = document.getElementById('pagination_next');
    var paginationStart;
    var currentPage = getParameterByName('page') || 1;
    var loadingPage = false;

    var loop = function(){
        var scrollY = window.pageYOffset;

        if (lastPosition == scrollY) {
            scroll(loop);
            return false;
        } else {
            lastPosition = scrollY;
        }

        var i = 0;
        for(i; i<size; i++) {       
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

            if(scrollY >= paginationStart && !loadingPage) {
                loadingPage = true;
                currentPage++
                var url = apiUrl + 'view/events/?page=' + currentPage;
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
                        refresh();
                    }
                }
                req.send(null);
            }
        };

        scroll( loop )
    };

    var refresh = function() {
        elements = document.getElementsByClassName('event-date');
        size = elements.length;
        var i = 0;
        for(i; i<size; i++) {
            matrix[i] = { el: elements[i] };
            matrix[i].el.style['position'] = '';
            matrix[i].el.style['top'] = '';
            matrix[i].height = matrix[i].el.offsetHeight
            matrix[i].start = matrix[i].el.offsetTop - startSpace;
        }
        paginationStart = documentHeight() - (window.innerHeight * 2) ;
        loadingPage = false;
    };

    window.onresize = refresh;

    refresh();
    loop();
};