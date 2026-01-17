(function(){
    'use strict';

    function getCsrf() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    async function fetchJson(url, opts = {}) {
        const headers = opts.headers || {};
        headers['X-Requested-With'] = 'XMLHttpRequest';
        const token = getCsrf();
        if (token) headers['X-CSRF-Token'] = token;

        const options = Object.assign({}, opts, { headers });

        try {
            const response = await fetch(url || window.location.href, options);
            const text = await response.text();
            try {
                const data = JSON.parse(text);
                return { ok: response.ok, status: response.status, data };
            } catch (e) {
                return { ok: response.ok, status: response.status, text };
            }
        } catch (err) {
            return { ok: false, status: 0, error: err };
        }
    }

    function _getToastContainer(){
        let container = document.querySelector('.toast-container');
        if (!container){
            container = document.createElement('div');
            container.className = 'toast-container';
            container.setAttribute('role', 'status');
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'false');
            document.body.appendChild(container);
        }
        return container;
    }

    function _createToast(msg, type = 'success', ttl = 5000){
        const container = _getToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;
        toast.innerHTML = `
            <div>
                <div class="toast__title">${type === 'success' ? 'Succès' : (type === 'danger' ? 'Erreur' : 'Info')}</div>
                <div class="toast__msg">${escapeHtml(msg)}</div>
            </div>
            <button class="btn-close" aria-label="Fermer">&times;</button>
        `;
        container.appendChild(toast);
        // Show animation
        requestAnimationFrame(() => toast.classList.add('toast--show'));

        const closeBtn = toast.querySelector('.btn-close');
        closeBtn.addEventListener('click', () => {
            toast.remove();
        });

        setTimeout(() => {
            // fade-out then remove
            toast.classList.remove('toast--show');
            setTimeout(() => toast.remove(), 250);
        }, ttl);
    }

    function escapeHtml(text){
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function notify(msg, type = 'success') {
        if (window.adminApp && typeof window.adminApp.showNotification === 'function') {
            window.adminApp.showNotification(msg, type);
        } else {
            _createToast(msg, type);
        }
    }

    window.appFetch = { getCsrf, fetchJson, notify, _createToast };
})();
