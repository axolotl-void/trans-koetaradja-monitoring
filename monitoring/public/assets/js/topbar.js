/* Topbar: dropdown notifikasi & profil, plus pintasan "/" */
(function () {
    'use strict';

    const pairs = [
        { btn: 'notifBtn', pop: 'notifPop' },
        { btn: 'userBtn', pop: 'userPop' }
    ];

    function closeAll(except) {
        pairs.forEach(function (p) {
            if (p.btn === except) return;

            const btn = document.getElementById(p.btn);
            const pop = document.getElementById(p.pop);

            if (!btn || !pop) return;

            pop.hidden = true;
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    pairs.forEach(function (p) {
        const btn = document.getElementById(p.btn);
        const pop = document.getElementById(p.pop);

        if (!btn || !pop) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();

            const akanBuka = pop.hidden;

            closeAll(p.btn);

            pop.hidden = !akanBuka;
            btn.setAttribute('aria-expanded', akanBuka ? 'true' : 'false');
        });

        pop.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    document.addEventListener('click', function () {
        closeAll(null);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAll(null);
            return;
        }

        // "/" fokus ke pencarian, kecuali sedang mengetik di input
        if (e.key === '/' ) {
            const tag = (document.activeElement.tagName || '').toLowerCase();
            const sedangInput = tag === 'input' || tag === 'textarea' || tag === 'select';

            if (sedangInput) return;

            const cari = document.querySelector('.topbar-search input');

            if (cari) {
                e.preventDefault();
                cari.focus();
            }
        }
    });
})();
