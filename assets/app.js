// assets/app.js
import './styles/app.scss';

// Import propre Bootstrap (classes dont tu as besoin)
import { Modal, Carousel, Popover } from 'bootstrap';

// (optionnel) exposer en global si tu as du legacy qui attend window.bootstrap
window.bootstrap = { Modal, Carousel, Popover };

// Protection mail
import './js/protection_mail';

// ✅ corrige le chemin : tiret et pas underscore
import './js/card-service';



document.addEventListener('DOMContentLoaded', () => {
  // ✅ Popover défini
  document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => new Popover(el));

  if (document.querySelector('#splash')) {
    initWaterIntro({ root: '#splash', canvas: '#splashCanvas', dpr: 1 });
  }

  syncSplashHeight({
    splashSelector: '.water-splash.match-height',
    cardSelector: '.services-card',
    breakpointPx: 992,
    minPx: 280,
  });
});
