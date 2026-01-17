/* inscription.js */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('inscriptionForm') || document.querySelector('form');
  if (!form) return;

  const pwd = form.querySelector('[name="password"]');
  const pwdConfirm = form.querySelector('[name="password_confirm"]');
  const strengthBar = document.querySelector('.pw-strength > i');

  function calcStrength(s){
    let score = 0;
    if (s.length >= 8) score += 1;
    if (/[A-Z]/.test(s)) score += 1;
    if (/[0-9]/.test(s)) score += 1;
    if (/[^A-Za-z0-9]/.test(s)) score += 1;
    return Math.min(100, score * 25);
  }

  // Update strength bar
  if (pwd && strengthBar) {
    pwd.addEventListener('input', () => {
      const pct = calcStrength(pwd.value);
      strengthBar.style.width = pct + '%';
      if (pct < 50) strengthBar.style.filter = 'grayscale(40%)';
      else strengthBar.style.filter = '';
    });
  }

  // Toggle visibility if toggle buttons present
  document.querySelectorAll('.password-toggle').forEach(btn =>
    btn.addEventListener('click', (e) => {
      const target = document.querySelector(btn.dataset.target);
      if (!target) return;
      target.type = (target.type === 'password') ? 'text' : 'password';
      btn.classList.toggle('active');
      e.preventDefault();
    })
  );

  function validateForm() {
    const errors = [];
    const nom = form.querySelector('[name="nom"]').value.trim();
    const email = form.querySelector('[name="mail"]').value.trim();
    if (!nom) errors.push('Le nom est requis');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Email invalide');
    if (pwd && pwd.value.length < 8) errors.push('Mot de passe trop court (min 8)');
    if (pwd && pwdConfirm && pwd.value !== pwdConfirm.value) errors.push('Les mots de passe ne correspondent pas');
    return errors;
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]') || form.querySelector('button[name="btn"]');
    const errors = validateForm();
    if (errors.length) {
      if (window.appFetch && typeof window.appFetch.notify === 'function') window.appFetch.notify(errors.join(' / '), 'danger');
      return;
    }

    if (btn) { btn.disabled = true; const original = btn.innerHTML; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> En cours...'; }

    const fd = new FormData(form);
    const res = await (window.appFetch ? window.appFetch.fetchJson(form.action || window.location.href, { method: 'POST', body: fd }) : fetch(form.action || window.location.href, { method: 'POST', body: fd }));

    if (btn) { btn.disabled = false; if (typeof original !== 'undefined') btn.innerHTML = original; }

    if (!res) return;
    if (!res.ok) {
      const msg = (res.data && res.data.errors) ? res.data.errors.join(' / ') : (res.text || 'Erreur serveur');
      if (window.appFetch && typeof window.appFetch.notify === 'function') window.appFetch.notify(msg, 'danger');
      return;
    }

    if (res.data && res.data.status === 'ok') {
      if (window.appFetch && typeof window.appFetch.notify === 'function') window.appFetch.notify('Inscription réussie. Vérifie ton email.', 'success');
      form.reset();
      if (strengthBar) strengthBar.style.width = '0%';
      if (res.data.redirect) setTimeout(() => (window.location.href = res.data.redirect), 900);
    } else {
      const msg = (res.data && res.data.errors) ? res.data.errors.join(' / ') : 'Erreur inattendue';
      if (window.appFetch && typeof window.appFetch.notify === 'function') window.appFetch.notify(msg, 'danger');
    }
  });
});