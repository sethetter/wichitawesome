(function() {

  // Ignore for unsupported browsers
  if (!(window.history && window.history.pushState)) return;

  // Main function that listens for clicks to the selector and opens the
  // href of the element in the iframe modal.
  //
  // @param {String} selector DOM query selector e.g. 'ul.list-items a'

  var scrollFrame = function(selector) {
    refreshOnNewIframePage();
    document.addEventListener('click', function(e) {
      // Ignore ctrl/cmd/shift clicks, as well as middle clicks
      if (e.ctrlKey || e.metaKey || e.shiftKey || e.which === 2) return;

      // Ignore if the element doesnt match our selector
      var els = document.querySelectorAll(selector);
      var elMatchesSelector = (window.Array || Array) // Hack for Zombie testing
        .prototype.filter.call(els, function(el) {
          return el == e.target || el.contains(e.target);
        }).length > 0
      if (!elMatchesSelector) return;

      // Get the href & open the iframe on that url
      var href = e.target.href || e.target.parentNode.href;
      if (href) {
        e.preventDefault();
        openIframe(href);
      }
    });
  }

  // Change pushState and open the iframe modal pointing to this url.
  //
  // @param {String} url

  var openIframe = function(url) {
    var prevHref = location.href;

    // Change the history
    history.pushState({ scrollFrame: true, href: location.href }, '', url);

    // Create the wrapper & iframe modal
    var body = document.getElementsByTagName('body')[0];
    var iOS = navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false;
    var attributes = [
      'position: fixed', 'top: 0', 'left: 0','width: 100%', 'height: 100%',
      'z-index: 10000000', 'background-color: white', 'border: 0'
    ];

    //only add scrolling fix for ios devices
    if (iOS){
      attributes.push('overflow-y: scroll');
      attributes.push('-webkit-overflow-scrolling: touch');
    }
    //create wrapper for iOS scroll fix
    var wrapper = document.createElement("div");
    wrapper.setAttribute('style',attributes.join(';'));
    var iframe = document.createElement("iframe");
    iframe.className = 'scroll-frame-iframe'
    iframe.setAttribute('style', [
      'width: 100%', 'height: 100%', 'position:absolute',
      'border: 0'
    ].join(';'));

    // Lock the body from scrolling & hide the body's scroll bars.
    body.setAttribute('style', 'overflow: hidden;' +
      (body.getAttribute('style') || ''));

    // Add a class to the body while the iframe loads then append it
    body.className += ' scroll-frame-loading';
    iframe.onload = function() {
      body.className = body.className.replace(' scroll-frame-loading', '');
    }
    wrapper.appendChild(iframe);
    body.appendChild(wrapper);
    iframe.contentWindow.location.replace(url);

    // On back-button remove the wrapper
    var onPopState = function(e) {
      if (location.href != prevHref) return;
      wrapper.removeChild(iframe);
      body.removeChild(wrapper);
      body.setAttribute('style',
        body.getAttribute('style').replace('overflow: hidden;', ''));
      removeEventListener('popstate', onPopState);
    }
    addEventListener('popstate', onPopState);
  }

  // To keep iframes from stacking up inside of each other and potentially
  // getting into a very messy state we'll use messaging b/t iframes to
  // signal when we've dived more than a page deep inside of our iframe modal
  // and cause the page to do a full refresh instead.

  var refreshOnNewIframePage = function() {
    addEventListener('message', function(e) {
      if (!e.data.href) return;
      if (!e.data.scrollFrame == true) return;
      if (e.data.href == this.location.href) return;
      var body = document.getElementsByTagName('body')[0];
      var html = document.getElementsByTagName('html')[0];
      this.location.assign(e.data.href);
    });
  }

  // Export for CommonJS & window global
  if (typeof module != 'undefined') {
    module.exports = scrollFrame;
  } else {
    window.scrollFrame = scrollFrame;
  }
})();
