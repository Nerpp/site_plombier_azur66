// assets/js/review-modal.js
document.addEventListener('click', (event) => {
  const trigger = event.target.closest('.js-review-modal');
  if (!trigger) return;

  if (!window.bootstrap || !window.bootstrap.Modal) {
    console.error('Bootstrap JS introuvable pour le modal avis.');
    return;
  }

  const modalEl = document.getElementById('reviewModal');
  if (!modalEl) return;

  const text = trigger.getAttribute('data-text') || '';
  const author = trigger.getAttribute('data-author') || 'Client';
  const source = trigger.getAttribute('data-source') || '';
  const rating = Number(trigger.getAttribute('data-rating') || 0);
  const age = trigger.getAttribute('data-age') || '';
  const visited = trigger.getAttribute('data-visited') || '';
  const label = trigger.getAttribute('data-label') || '';

  const authorEl = modalEl.querySelector('.js-review-author');
  const ratingEl = modalEl.querySelector('.js-review-rating');
  const sourceEl = modalEl.querySelector('.js-review-source');
  const metaEl = modalEl.querySelector('.js-review-meta');
  const textEl = modalEl.querySelector('.js-review-text');

  if (authorEl) authorEl.textContent = author;
  if (ratingEl) {
    ratingEl.textContent = '★'.repeat(Math.max(0, Math.min(5, rating)));
  }
  if (sourceEl) {
    sourceEl.textContent = source ? `— ${source}` : '';
  }

  const metaParts = [];
  if (label) metaParts.push(label);
  if (age) metaParts.push(age);
  if (visited) metaParts.push(visited);
  if (metaEl) metaEl.textContent = metaParts.join(' · ');

  if (textEl) textEl.textContent = text;

  const modal = new window.bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
  modal.show();
});
