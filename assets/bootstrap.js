// assets/bootstrap.js
import { startStimulusApp } from '@symfony/stimulus-bridge';

// Enregistre les contrôleurs depuis controllers.json et le dossier ./controllers
export const app = startStimulusApp(require.context(
  '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
  true,
  /\.(j|t)sx?$/
));

// // Tu peux ensuite enregistrer des contrôleurs tiers ici si besoin
// app.register('some', SomeImportedController);
