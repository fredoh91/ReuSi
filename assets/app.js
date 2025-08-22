import 'bootstrap';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

// console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

import { startStimulusApp } from '@symfony/stimulus-bridge';
// import '@symfony/autoimport';

// Importez le contrôleur UX Toggle Password
import '@symfony/ux-toggle-password';

// Démarrez l'application Stimulus
const app = startStimulusApp();


