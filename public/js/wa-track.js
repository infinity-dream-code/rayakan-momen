(function () {
    var endpointEl = document.querySelector('meta[name="wa-click-url"]');
    if (!endpointEl || !endpointEl.content) return;

    var endpoint = endpointEl.content.replace(/\/$/, '');
    var waNumEl = document.querySelector('meta[name="wa-number"]');
    var needle = (waNumEl && waNumEl.content) ? waNumEl.content : '6285777433886';

    function isOurWaLink(href) {
        if (!href || href.indexOf('wa.me') === -1) return false;
        if (href.indexOf(needle) !== -1) return true;
        try {
            var u = new URL(href, window.location.origin);
            var path = (u.pathname || '').replace(/\D/g, '');
            return path.indexOf(needle) !== -1;
        } catch (e) {
            return false;
        }
    }

    function ping() {
        var url = endpoint + (endpoint.indexOf('?') > -1 ? '&' : '?') + 't=' + Date.now();
        if (typeof fetch === 'function') {
            fetch(url, { method: 'GET', keepalive: true, credentials: 'same-origin' }).catch(function () {});
            return;
        }
        try {
            (new Image()).src = url;
        } catch (e) {}
    }

    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href*="wa.me"]');
        if (!a || a.dataset.waTracked === '1') return;
        if (!isOurWaLink(a.href || a.getAttribute('href') || '')) return;
        a.dataset.waTracked = '1';
        ping();
    }, true);
})();
