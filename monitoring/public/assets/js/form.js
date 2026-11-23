/* Infrastruktur form bersama: validasi klien + modal konfirmasi hapus.
 *
 * API:
 *   <form data-validate>              -> aktifkan validasi klien
 *     input[required]                 -> wajib diisi
 *     input[type=number][min][max]    -> cek rentang
 *     input[data-min-km="N"]          -> nilai tidak boleh < N
 *   <form data-confirm="Pesan">       -> modal konfirmasi sebelum submit
 *   <button type=submit data-confirm="Pesan"> -> konfirmasi khusus tombol ini
 *   <a data-confirm="Pesan" data-confirm-label="Hapus"> -> konfirmasi navigasi
 */
(function () {
    'use strict';

    var forms = document.querySelectorAll('form');
    if (!forms.length) {
        return;
    }

    // Validasi browser (bubble bawaan) menahan submit sebelum handler kita jalan.
    // Matikan hanya untuk form yang ikut validasi klien, dan hanya kalau JS hidup,
    // supaya kalau JS mati validasi HTML5 tetap bekerja sebagai jaring pengaman.
    for (var fi = 0; fi < forms.length; fi++) {
        if (forms[fi].hasAttribute('data-validate')) {
            forms[fi].setAttribute('novalidate', 'novalidate');
        }
    }

    function groupOf(el) {
        return el.closest('.form-group') || el.parentElement;
    }

    function radiosChecked(el) {
        if (!el.name) {
            return el.checked;
        }
        var scope = el.form || document;
        var list = scope.querySelectorAll('input[type="radio"]');
        for (var i = 0; i < list.length; i++) {
            if (list[i].name === el.name && list[i].checked) {
                return true;
            }
        }
        return false;
    }

    function msgFor(el) {
        if (el.disabled || el.type === 'hidden' || el.type === 'submit' || el.type === 'button') {
            return '';
        }

        if (el.hasAttribute('required')) {
            var empty = (el.type === 'checkbox' || el.type === 'radio')
                ? !radiosChecked(el)
                : !String(el.value || '').trim();
            if (empty) {
                return 'Wajib diisi.';
            }
        }

        if (el.value !== '' && el.value !== null) {
            var v = parseFloat(el.value);
            if (el.type === 'number' && el.hasAttribute('min') && v < parseFloat(el.getAttribute('min'))) {
                return 'Nilai minimal ' + el.getAttribute('min') + '.';
            }
            if (el.type === 'number' && el.hasAttribute('max') && v > parseFloat(el.getAttribute('max'))) {
                return 'Nilai maksimal ' + el.getAttribute('max') + '.';
            }
            var minKm = el.getAttribute('data-min-km');
            if (minKm !== null && !isNaN(v) && v < parseFloat(minKm)) {
                return 'Tidak boleh kurang dari ' + minKm + ' km.';
            }
        }

        return '';
    }

    function setError(el, msg) {
        var g = groupOf(el);
        if (!g) {
            return;
        }
        g.classList.add('has-error');

        var old = g.querySelectorAll('.js-field-error');
        for (var i = 0; i < old.length; i++) {
            old[i].parentNode.removeChild(old[i]);
        }

        var hint = document.createElement('div');
        hint.className = 'field-hint js-field-error';
        hint.textContent = msg;

        var anchor = (el.closest('.input-wrap') && el.closest('.input-wrap').parentNode === g)
            ? el.closest('.input-wrap')
            : el;
        anchor.parentNode.insertBefore(hint, anchor.nextSibling);
    }

    function clearError(el) {
        var g = groupOf(el);
        if (!g) {
            return;
        }
        g.classList.remove('has-error');
        var old = g.querySelectorAll('.js-field-error');
        for (var i = 0; i < old.length; i++) {
            old[i].parentNode.removeChild(old[i]);
        }
    }

    function validate(form) {
        var fields = form.querySelectorAll('input, select, textarea');
        var ok = true;
        for (var i = 0; i < fields.length; i++) {
            var msg = msgFor(fields[i]);
            if (msg) {
                setError(fields[i], msg);
                ok = false;
            } else {
                clearError(fields[i]);
            }
        }
        return ok;
    }

    // --- tombol submit: cegah klik ganda ---
    function lock(form) {
        var btns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        for (var i = 0; i < btns.length; i++) {
            var b = btns[i];
            if (b.dataset.jsText === undefined) {
                b.dataset.jsText = b.tagName === 'INPUT' ? b.value : b.textContent;
            }
            b.disabled = true;
            if (b.tagName === 'INPUT') {
                b.value = 'Menyimpan...';
            } else {
                b.textContent = 'Menyimpan...';
            }
        }
    }

    function unlock(form) {
        var btns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        for (var i = 0; i < btns.length; i++) {
            var b = btns[i];
            if (b.dataset.jsText === undefined) {
                continue;
            }
            b.disabled = false;
            if (b.tagName === 'INPUT') {
                b.value = b.dataset.jsText;
            } else {
                b.textContent = b.dataset.jsText;
            }
            delete b.dataset.jsText;
        }
    }

    function finalize(form, submitter) {
        // submitter yang di-disable tidak andal di requestSubmit(); titip name/value lewat hidden input
        if (submitter && submitter.name) {
            var tmp = document.createElement('input');
            tmp.type = 'hidden';
            tmp.name = submitter.name;
            tmp.value = submitter.value;
            form.appendChild(tmp);
        }

        form.dataset.jsConfirmed = '1';
        lock(form);

        // requestSubmit() TIDAK boleh dipanggil di dalam handler event submit:
        // spesifikasi HTML menolak submission bersarang, jadi request tidak terkirim.
        // Tunda satu tick supaya submission sebelumnya benar-benar selesai.
        var submitNow = function () {
            if (typeof form.requestSubmit === 'function' && submitter && !submitter.disabled) {
                form.requestSubmit(submitter);
            } else if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        };

        setTimeout(submitNow, 0);
    }

    // --- modal konfirmasi (pakai bootstrap yang sudah ada di layout) ---
    var modalEl = null;
    var modalText = null;
    var modalOk = null;

    function buildModal() {
        if (modalEl || !window.bootstrap) {
            return;
        }
        var wrap = document.createElement('div');
        wrap.className = 'modal fade';
        wrap.id = 'jsConfirmModal';
        wrap.tabIndex = -1;
        wrap.setAttribute('aria-hidden', 'true');
        wrap.innerHTML =
            '<div class="modal-dialog modal-dialog-centered modal-sm">' +
            '<div class="modal-content">' +
            '<div class="modal-body" id="jsConfirmText"></div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn-secondary" data-bs-dismiss="modal">Batal</button>' +
            '<button type="button" class="btn-primary" id="jsConfirmOk">Hapus</button>' +
            '</div></div></div>';
        document.body.appendChild(wrap);

        modalEl = wrap;
        modalText = wrap.querySelector('#jsConfirmText');
        modalOk = wrap.querySelector('#jsConfirmOk');
    }

    function askConfirm(message, label, onOk) {
        if (!window.bootstrap) {
            if (window.confirm(message)) {
                onOk();
            }
            return;
        }

        buildModal();
        modalText.textContent = message;
        modalOk.textContent = label || 'Hapus';

        var inst = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        modalOk.onclick = function () {
            inst.hide();
            onOk();
        };
        inst.show();
    }

    // --- submit ---
    function onSubmit(e) {
        var form = e.target;
        if (!form || !form.tagName || form.tagName !== 'FORM') {
            return;
        }
        if (!form.hasAttribute('data-validate') && !form.hasAttribute('data-confirm')) {
            return;
        }
        if (form.dataset.jsConfirmed === '1') {
            return; // lanjut submit native
        }

        e.preventDefault();

        if (form.hasAttribute('data-validate') && !validate(form)) {
            unlock(form);
            return;
        }

        var submitter = e.submitter || null;
        var msg = (submitter && submitter.getAttribute('data-confirm')) || form.getAttribute('data-confirm');

        if (msg) {
            var label = (submitter && submitter.getAttribute('data-confirm-label')) || form.getAttribute('data-confirm-label');
            askConfirm(msg, label, function () {
                finalize(form, submitter);
            });
            return;
        }

        finalize(form, submitter);
    }

    document.addEventListener('submit', onSubmit);

    // --- konfirmasi untuk link di luar form ---
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest ? e.target.closest('[data-confirm]') : null;
        if (!trigger || trigger.tagName === 'FORM' || trigger.closest('form')) {
            return;
        }
        var href = trigger.getAttribute('href');
        if (!href || href.charAt(0) === '#') {
            return;
        }
        e.preventDefault();
        askConfirm(trigger.getAttribute('data-confirm'), trigger.getAttribute('data-confirm-label'), function () {
            window.location.href = trigger.href;
        });
    });

    // --- live re-validate field yang sudah error ---
    function onFieldChange(e) {
        var el = e.target;
        if (!el || !el.form || !el.form.hasAttribute('data-validate')) {
            return;
        }
        var g = groupOf(el);
        if (!g || !g.classList.contains('has-error')) {
            return;
        }
        var msg = msgFor(el);
        if (msg) {
            setError(el, msg);
        } else {
            clearError(el);
        }
    }

    document.addEventListener('input', onFieldChange);
    document.addEventListener('change', onFieldChange);

    // tombol jangan tertinggal disabled saat halaman dipulihkan dari bfcache
    window.addEventListener('pageshow', function () {
        var locked = document.querySelectorAll('form button[type="submit"][disabled], form input[type="submit"][disabled]');
        for (var i = 0; i < locked.length; i++) {
            if (locked[i].dataset.jsText !== undefined && locked[i].form) {
                locked[i].form.dataset.jsConfirmed = '';
                unlock(locked[i].form);
            }
        }
    });
})();
