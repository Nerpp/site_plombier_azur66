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

// empeche les images de zoomer sur mobile
// import './js/prevent-zoom-mobile';


