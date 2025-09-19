import './assets/main.css';
import 'flowbite';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { install as VueReCaptcha } from 'vue3-recaptcha-v2';
import App from './App.vue';
import router from './router/router.js';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.core.css';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

// Font Awesome setup...
import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faBars, faPhone, faBell, faTimes, faEdit, faTrash, faTimesCircle, faUserCircle,
  faPlus, faCreditCard, faEnvelope, faBullhorn, faInbox, faCalendarCheck, faUsers,
  faTachometerAlt, faHeadset, faRightFromBracket, faHourglass, faHourglassHalf,
  faChevronCircleDown, faCheckCircle, faUserPlus
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faFacebook, faTwitter, faInstagram, faTiktok, faYoutube } from '@fortawesome/free-brands-svg-icons';

library.add(
  faFacebook, faPhone, faTimesCircle, faCreditCard, faUserCircle,
  faTwitter, faInstagram, faTiktok, faYoutube, faBars, faTimes,
  faEdit, faTrash, faPlus, faUserPlus, faCheckCircle, faBell,
  faHourglassHalf, faEnvelope, faBullhorn, faHourglass, faInbox,
  faCalendarCheck, faUsers, faTachometerAlt, faHeadset, faRightFromBracket
);

const app = createApp(App);
const pinia = createPinia();
app.use(pinia);
app.component('font-awesome-icon', FontAwesomeIcon);
app.use(useToast);
app.use(router);

app.use(VueReCaptcha, {
  siteKey: '6LeVRM4rAAAAACZLcBp_o6lByka2W0R9xPqBgc1f',
});
app.mount('#app');
