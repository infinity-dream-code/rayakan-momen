(function () {
    var endpoint = document.querySelector('meta[name="wa-click-url"]');
    if (!endpoint || !endpoint.content) return;

    var waNum = document.querySelector('meta[name="wa-number"]');
    var needle = waNum && waNum.content ? waNum.content : '6285777433886';

    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href*="wa.me"]');
        if (!a || a.href.indexOf(needle) === -1) return;

        try {
            if (navigator.sendBeacon) {
                navigator.sendBeacon(endpoint.content, new Blob(['{}'], { type: 'application/json' }));
            } else {
                fetch(endpoint.content, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
                    body: '{}',
                    keepalive: true,
                }).catch(function () {});
            }
        } catch (err) {}
    }, true);
})();
