/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import Vue from 'vue';
import Vuetify from 'vuetify';
import Routes from './js/routes.js';
import App from './js/views/App';
import Vuelidate from 'vuelidate'
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuelidate)
Vue.use(Vuetify);

const app = new Vue({
  el: '#app',
  router: Routes,
  render: h => h(App),
  vuetify : new Vuetify(),
});

export default app;