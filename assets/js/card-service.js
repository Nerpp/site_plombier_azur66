// assets/js/card-service.js
// Utilise les classes importées via window.bootstrap (exposées dans app.js)
document.addEventListener('click', (e) => {
  const trigger = e.target.closest('.service-lightbox-trigger');
  if (!trigger) return;

  // Ignorer clics sur les contrôles/indicateurs du petit carousel
  if (e.target.closest('.carousel-control-prev, .carousel-control-next, .carousel-indicators button')) return;

  e.preventDefault();

  if (!window.bootstrap || !window.bootstrap.Modal || !window.bootstrap.Carousel) {
    console.error('Bootstrap JS introuvable. Charge bootstrap.bundle.min.js ou expose window.bootstrap via Encore.');
    alert('Lightbox indisponible : Bootstrap JS manquant.');
    return;
  }

  const items = trigger.querySelectorAll('.carousel-item');
  if (!items.length) return;

  const modalEl = document.getElementById('lightboxModal');
  if (!modalEl) {
    console.error('#lightboxModal introuvable');
    return;
  }

  const indicators = modalEl.querySelector('.carousel-indicators');
  const inner = modalEl.querySelector('.carousel-inner');
  if (!indicators || !inner) {
    console.error('Structure du modal incomplète (indicators/inner)');
    return;
  }

  indicators.innerHTML = '';
  inner.innerHTML = '';

  // Slide actif dans le carousel cliqué
  let activeIndex = 0;
  items.forEach((it, idx) => {
    if (it.classList.contains('active')) activeIndex = idx;
  });

  items.forEach((it, idx) => {
    const img = it.querySelector('img');
    const caption = it.querySelector('.carousel-caption')?.innerHTML || '';
    const prefersLarge = window.matchMedia('(min-width: 992px)').matches;
    const full = prefersLarge
      ? (img?.getAttribute('data-full') || img?.src || '')
      : (img?.src || img?.getAttribute('data-full') || '');

    // Indicateur
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.setAttribute('data-bs-target', '#lightboxCarousel');
    btn.setAttribute('data-bs-slide-to', String(idx));
    btn.className = (idx === activeIndex) ? 'active' : '';
    if (idx === activeIndex) btn.setAttribute('aria-current', 'true');
    btn.setAttribute('aria-label', 'Slide ' + (idx + 1));
    indicators.appendChild(btn);

    // Slide
    const slide = document.createElement('div');
    slide.className = 'carousel-item' + (idx === activeIndex ? ' active' : '');

    const ratio = document.createElement('div');
    ratio.className = 'ratio ratio-16x9';

    const big = document.createElement('img');
    big.className = 'w-100 h-100';
    big.style.objectFit = 'contain';
    big.style.backgroundColor = '#f8f9fa';
    big.loading = (idx === activeIndex) ? 'eager' : 'lazy';
    big.alt = img?.alt || '';
    big.src = full;

    ratio.appendChild(big);
    slide.appendChild(ratio);

    if (caption) {
      const cap = document.createElement('div');
      cap.className = 'carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2';
      cap.innerHTML = caption;
      slide.appendChild(cap);
    }

    inner.appendChild(slide);
  });

  // Afficher/masquer contrôles/indicateurs si 1 seule image
  const hasMultiple = items.length > 1;
  ['.carousel-control-prev', '.carousel-control-next', '.carousel-indicators'].forEach(sel => {
    const el = modalEl.querySelector(sel);
    if (el) el.style.display = hasMultiple ? '' : 'none';
  });

  // (Ré)initialiser carousel du modal
  const modalCarouselEl = document.getElementById('lightboxCarousel');
  if (!modalCarouselEl) {
    console.error('#lightboxCarousel introuvable');
    return;
  }

  const existing = window.bootstrap.Carousel.getInstance(modalCarouselEl);
  if (existing) existing.dispose();

  new window.bootstrap.Carousel(modalCarouselEl, { interval: false, wrap: true });

  // Ouvrir le modal
  const modal = new window.bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
  modal.show();
});
