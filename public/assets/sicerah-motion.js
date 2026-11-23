(() => {
    const loader = document.getElementById('sc-page-loader');
    if (!loader) return;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let hideTimer;
    let safetyTimer;
    const hide = () => {
        window.clearTimeout(hideTimer);
        window.clearTimeout(safetyTimer);
        loader.classList.remove('is-visible');
        loader.setAttribute('aria-hidden', 'true');
    };
    const show = () => {
        window.clearTimeout(hideTimer);
        window.clearTimeout(safetyTimer);
        loader.classList.add('is-visible');
        loader.setAttribute('aria-hidden', 'false');
        // Recover if navigation is cancelled or the destination is a download.
        safetyTimer = window.setTimeout(hide, 8000);
    };
    let firstVisit = true;
    try {
        firstVisit = !sessionStorage.getItem('sicerah-welcomed');
        sessionStorage.setItem('sicerah-welcomed', '1');
    } catch (_) { /* Storage can be unavailable in private browsing. */ }
    const started = performance.now();
    show();
    const ready = () => {
        hideTimer = window.setTimeout(hide, Math.max(0, (firstVisit && !reducedMotion ? 1100 : 0) - (performance.now() - started)));
    };
    if (document.readyState === 'complete') ready();
    else window.addEventListener('load', ready, { once: true });
    window.addEventListener('pageshow', (event) => { if (event.persisted) hide(); });
    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest('a[href]');
        if (!link || link.hasAttribute('download') || link.hasAttribute('data-no-loader') || (link.target && link.target !== '_self')) return;
        const destination = new URL(link.href, location.href);
        if (!['http:', 'https:'].includes(destination.protocol) || destination.origin !== location.origin) return;
        if (destination.pathname === location.pathname && destination.search === location.search) return;
        if (/\.(pdf|xlsx?|csv|zip|docx?|png|jpe?g)$/i.test(destination.pathname)) return;
        show();
    });
    document.addEventListener('submit', (event) => {
        const form = event.target;
        const target = event.submitter?.formTarget || form.target;
        if (event.defaultPrevented || (target && target !== '_self') || form.hasAttribute('data-no-loader') || form.method === 'dialog') return;
        const destination = new URL(event.submitter?.formAction || form.action, location.href);
        if (destination.origin === location.origin) show();
    });
})();
