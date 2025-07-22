/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import { createApp } from 'vue';
import App from './js/App.vue';

App.options = {
    host: document.getElementById('isps-reader').getAttribute('data-host'),
    sources: document.getElementById('isps-reader').getAttribute('data-sources')
};

createApp(App).mount('#isps-reader');