/**
 * PROFESSIONAL SOFTWARE - Visitor Tracker
 * Лёгкий счётчик посетителей для SPA
 */
(function() {
    'use strict';
    
    // Определяем base URL автоматически
    var script = document.currentScript || document.querySelector('script[src*="tracker.js"]');
    var scriptSrc = script ? script.src : '';
    var basePath = scriptSrc.replace(/\/tracker\.js.*$/, '');
    var apiUrl = basePath + '/api/track.php';
    
    var lastUrl = '';
    var startTime = Date.now();
    
    function track(action, extraData) {
        var data = Object.assign({
            action: action,
            url: window.location.href,
            title: document.title,
            referer: document.referrer
        }, extraData || {});
        
        try {
            // Используем sendBeacon для надёжности при закрытии страницы
            if (action === 'heartbeat' && navigator.sendBeacon) {
                var blob = new Blob([JSON.stringify(data)], {type: 'application/json'});
                navigator.sendBeacon(apiUrl, blob);
            } else {
                fetch(apiUrl, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data),
                    keepalive: true
                }).catch(function(){});
            }
        } catch(e) {}
    }
    
    function trackPageview() {
        var currentUrl = window.location.href;
        if (currentUrl === lastUrl) return;
        lastUrl = currentUrl;
        track('pageview');
    }
    
    // Первоначальный заход
    trackPageview();
    
    // Отслеживаем изменения hash (HashRouter)
    window.addEventListener('hashchange', function() {
        setTimeout(trackPageview, 50);
    });
    
    // Отслеживаем popstate (BrowserRouter)
    window.addEventListener('popstate', function() {
        setTimeout(trackPageview, 50);
    });
    
    // Patch для history API (для React Router)
    var origPushState = history.pushState;
    history.pushState = function() {
        origPushState.apply(this, arguments);
        setTimeout(trackPageview, 50);
    };
    
    var origReplaceState = history.replaceState;
    history.replaceState = function() {
        origReplaceState.apply(this, arguments);
        setTimeout(trackPageview, 50);
    };
    
    // Heartbeat каждые 15 секунд
    setInterval(function() {
        if (!document.hidden) {
            track('heartbeat');
        }
    }, 15000);
    
    // Финальный heartbeat при закрытии
    window.addEventListener('beforeunload', function() {
        track('heartbeat');
    });
    
    // Heartbeat при возврате на вкладку
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            track('heartbeat');
        }
    });
})();
