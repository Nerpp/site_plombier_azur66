document.addEventListener('click', (e) => {
  const a = e.target.closest('.js-mail');
  if (!a) return;
  e.preventDefault();
  const email = `${a.dataset.user}@${a.dataset.domain}.${a.dataset.tld}`;
  const href  = `mailto:${email}?subject=${a.dataset.subject}&body=${a.dataset.body}`;
  a.setAttribute('href', href);
  a.setAttribute('aria-label', `Envoyer un e-mail à ${email}`);
  window.location.href = href;
}, { passive:false });

// Accessibilité clavier (Entrée/Espace)
document.addEventListener('keydown', (e) => {
  const a = e.target.closest('.js-mail');
  if (!a) return;
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    a.click();
  }
});