/**
 * Global Form Submission Handler (v6)
 *
 * Mencegah double submit dengan menonaktifkan tombol saat form diproses.
 *
 * Cara form dibuka kembali (unlock), sesuai jenis submit:
 *
 * 1. Submit biasa (navigasi halaman, mis. simpan / cari GET)
 *    -> halaman reload, state otomatis bersih.
 *
 * 2. Submit yang menghasilkan DOWNLOAD (Export Excel / Cetak PDF)
 *    -> halaman TIDAK reload, jadi browser tidak memberi sinyal apa pun.
 *       Solusinya "download token": JS menyisipkan hidden input `download_token`,
 *       server membalas dengan cookie `download_token` bernilai sama,
 *       JS mendeteksi cookie itu lalu unlock. (Butuh middleware di Laravel.)
 *       Tanpa cookie, tombol dibuka kembali oleh fallback timeout.
 *
 * 3. Submit via AJAX (handler lain memanggil preventDefault lalu fetch/XHR)
 *    -> unlock otomatis saat semua request yang dimulai setelah submit selesai.
 *
 * Tambahan: pageshow (bfcache) & popstate (tombol Back) juga membuka kunci.
 *
 * Opsi di HTML:
 *   data-loading-text="Memproses..."  -> teks tombol saat loading
 *   data-download="true"              -> paksa form/tombol dianggap download
 *
 * API: window.FormSubmitGuard.unlock(form) / .unlockAll()
 */
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    // ===== Konfigurasi =====
    const CONFIG = {
        debug: false,
        submitSelectors: [
            'button[type="submit"]',
            'button.btn-submit',
            'button.btn-save',
            'button.btn-simpan',
            'input[type="submit"]',
        ],
        cancelSelectors: [
            'button[type="reset"]',
            'button.btn-cancel',
            'button.btn-batal',
            'a.btn-cancel',
            'a.btn-back',
        ],
        downloadKeywords: ['export', 'download', 'cetak'], // dicocokkan ke action / formaction
        tokenName: 'download_token',      // nama hidden input DAN nama cookie
        tokenPollInterval: 500,           // ms
        failsafeTimeout: 3000,            // 3 detik (submit biasa) - fast reset
        downloadFallbackTimeout: 3000,    // 3 detik fallback jika cookie tidak datang
        ajaxGraceTimeout: 3000,           // unlock jika tidak ada request AJAX dalam 3 detik
        loadingText: 'Menyimpan...',
        downloadText: 'Memproses...',
        spinnerClass: 'fas fa-spinner fa-spin',
    };

    const log = (...args) => CONFIG.debug && console.log('[FormSubmitGuard]', ...args);

    // form -> entry { form, controls, pending, started, ajaxMode, timerId, graceId, pollId }
    const lockedForms = new Map();
    // entry yang sedang memantau request AJAX
    const watchers = new Set();

    // ===== Helper umum =====
    const queryAll = (root, selectors) =>
        [...new Set(selectors.flatMap(sel => Array.from(root.querySelectorAll(sel))))];

    const isHidden = el => el.style.display === 'none';

    const getContent = el => (el.tagName === 'INPUT' ? el.value : el.innerHTML);

    function isDownloadSubmit(form, submitter) {
        if (submitter?.dataset.download === 'true' || form.dataset.download === 'true') {
            return true;
        }

        const action = submitter?.getAttribute('formaction') || form.getAttribute('action') || '';
        const target = submitter?.getAttribute('formtarget') || form.getAttribute('target');

        return (
            target === '_blank' ||
            CONFIG.downloadKeywords.some(keyword => action.toLowerCase().includes(keyword))
        );
    }

    // ===== Cookie & token =====
    const createToken = () =>
        Date.now().toString(36) + Math.random().toString(36).slice(2, 10);

    function readCookie(name) {
        const prefix = `${name}=`;
        const part = document.cookie.split('; ').find(c => c.startsWith(prefix));
        return part ? decodeURIComponent(part.slice(prefix.length)) : null;
    }

    function expireCookie(name) {
        document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
    }

    function setTokenInput(form, token) {
        let input = form.querySelector(`input[name="${CONFIG.tokenName}"]`);

        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = CONFIG.tokenName;
            form.appendChild(input);
        }

        input.value = token;
    }

    function watchDownloadToken(entry, token) {
        entry.pollId = setInterval(() => {
            if (readCookie(CONFIG.tokenName) === token) {
                log('Cookie download diterima - unlock', entry.form);
                expireCookie(CONFIG.tokenName);
                unlockForm(entry.form);
            }
        }, CONFIG.tokenPollInterval);
    }

    // ===== Kontrol tombol =====
    function disableControl(el) {
        el.disabled = true;
        el.setAttribute('aria-disabled', 'true');
        el.style.pointerEvents = 'none';
        el.style.cursor = 'not-allowed';
        el.classList.add('disabled');
    }

    function enableControl(el) {
        el.disabled = false;
        el.removeAttribute('aria-disabled');
        el.style.pointerEvents = '';
        el.style.cursor = '';
        el.classList.remove('disabled', 'btn-loading');
    }

    function showLoading(el, text) {
        el.classList.add('btn-loading');

        if (el.tagName === 'INPUT') {
            el.value = text; // <input> tidak punya innerHTML
        } else {
            el.innerHTML = `<i class="${CONFIG.spinnerClass} mr-2"></i>${text}`;
        }
    }

    function restoreContent({ el, original }) {
        if (original === null) return;

        if (el.tagName === 'INPUT') {
            el.value = original;
        } else {
            el.innerHTML = original;
        }
    }

    // ===== Lock / Unlock =====
    function applyLock(entry, submitter, downloadMode) {
        const { form } = entry;
        const submitBtns = queryAll(form, CONFIG.submitSelectors);
        const cancelBtns = queryAll(form, CONFIG.cancelSelectors);

        if (submitter && !submitBtns.includes(submitter)) {
            submitBtns.push(submitter);
        }

        submitBtns.forEach(el => {
            if (el.disabled || isHidden(el)) return;

            // Hanya tombol yang diklik yang menampilkan spinner;
            // tombol submit lain cukup dinonaktifkan.
            const showSpinner = !submitter || el === submitter;
            const text =
                el.dataset.loadingText ||
                (downloadMode ? CONFIG.downloadText : CONFIG.loadingText);

            entry.controls.push({ el, original: showSpinner ? getContent(el) : null });
            disableControl(el);
            if (showSpinner) showLoading(el, text);
        });

        cancelBtns.forEach(el => {
            if (el.disabled || isHidden(el) || entry.controls.some(c => c.el === el)) return;

            entry.controls.push({ el, original: null });
            disableControl(el);
        });

        const timeout = downloadMode ? CONFIG.downloadFallbackTimeout : CONFIG.failsafeTimeout;
        entry.timerId = setTimeout(() => {
            log('Timeout - form dibuka kembali', form);
            unlockForm(form);
        }, timeout);

        log(`${entry.controls.length} tombol dinonaktifkan`, form);
    }

    function unlockForm(form) {
        const entry = lockedForms.get(form);
        if (!entry) return;

        clearTimeout(entry.timerId);
        clearTimeout(entry.graceId);
        clearInterval(entry.pollId);
        watchers.delete(entry);

        entry.controls.forEach(control => {
            enableControl(control.el);
            restoreContent(control);
        });

        lockedForms.delete(form);
        log('Form dibuka kembali', form);
    }

    function unlockAll() {
        Array.from(lockedForms.keys()).forEach(unlockForm);
    }

    // ===== Pelacakan request AJAX (fetch & XHR) =====
    function settleAjax(entry) {
        if (entry.ajaxMode && entry.started && entry.pending.size === 0) {
            log('Semua request AJAX selesai - unlock', entry.form);
            unlockForm(entry.form);
        }
    }

    function trackRequestStart() {
        const id = Symbol('request');

        watchers.forEach(entry => {
            entry.pending.add(id);
            entry.started = true;
        });

        return function onRequestEnd() {
            Array.from(watchers).forEach(entry => {
                entry.pending.delete(id);
                settleAjax(entry);
            });
        };
    }

    function setupRequestTracking() {
        const originalFetch = window.fetch;

        if (typeof originalFetch === 'function') {
            window.fetch = function (...args) {
                const done = trackRequestStart();
                const promise = originalFetch.apply(this, args);

                promise.then(done, done); // error tetap ditangani oleh pemanggil
                return promise;
            };
        }

        const originalSend = XMLHttpRequest.prototype.send;

        XMLHttpRequest.prototype.send = function (...args) {
            const done = trackRequestStart();

            this.addEventListener('loadend', done, { once: true });
            return originalSend.apply(this, args);
        };
    }

    // ===== Guard submit =====
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', event => {
            // Blokir jika form sudah pernah di-submit
            if (lockedForms.has(form)) {
                log('Submit diblokir - form sedang diproses', form);
                event.preventDefault();
                event.stopImmediatePropagation();
                return;
            }

            const submitter = event.submitter || null;
            const downloadMode = isDownloadSubmit(form, submitter);

            const entry = {
                form,
                controls: [],
                pending: new Set(),
                started: false,
                ajaxMode: false,
                timerId: null,
                graceId: null,
                pollId: null,
            };

            // Kunci segera agar submit kedua langsung ditolak
            lockedForms.set(form, entry);
            watchers.add(entry);

            // Token harus disisipkan SEKARANG, sebelum browser membentuk data form
            let token = null;
            if (downloadMode) {
                token = createToken();
                setTokenInput(form, token);
            }

            // Nonaktifkan tombol setelah event selesai, supaya name/value
            // tombol submit tetap ikut terkirim ke server.
            setTimeout(() => {
                if (lockedForms.get(form) !== entry) return;

                applyLock(entry, submitter, downloadMode);

                if (event.defaultPrevented) {
                    // Ditangani JS (AJAX / validasi kustom / konfirmasi)
                    entry.ajaxMode = true;

                    if (entry.started) {
                        settleAjax(entry);
                    } else {
                        entry.graceId = setTimeout(() => {
                            if (!entry.started) unlockForm(form);
                        }, CONFIG.ajaxGraceTimeout);
                    }
                    return;
                }

                // Submit native (navigasi atau download)
                watchers.delete(entry);
                if (downloadMode) watchDownloadToken(entry, token);
            }, 0);
        });
    });

    setupRequestTracking();

    // Back/forward: halaman dipulihkan dari bfcache atau history berubah
    window.addEventListener('pageshow', event => {
        if (event.persisted) unlockAll();
    });
    window.addEventListener('popstate', unlockAll);

    // ===== Styling loading =====
    function injectStyles() {
        if (document.getElementById('form-submit-guard-style')) return;

        const style = document.createElement('style');
        style.id = 'form-submit-guard-style';
        style.textContent = `
            button.btn-loading,
            input.btn-loading {
                opacity: 0.7;
                cursor: not-allowed !important;
                pointer-events: none;
            }

            button.btn-loading:hover,
            input.btn-loading:hover {
                background-color: inherit;
                border-color: inherit;
            }
        `;
        document.head.appendChild(style);
    }

    injectStyles();

    window.FormSubmitGuard = { unlock: unlockForm, unlockAll };
});