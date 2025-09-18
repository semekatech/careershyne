import './assets/main.css'
import 'flowbite'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router/router.js'
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import 'quill/dist/quill.snow.css'; // Add this
import 'quill/dist/quill.core.css'; // Optional but can help
import '@vueup/vue-quill/dist/vue-quill.snow.css'
// Font Awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBars,faPhone,faBell,faTimes, faEdit, faTrash,faTimesCircle,faUserCircle,faPlus,faCreditCard, faEnvelope, faBullhorn, faInbox, faCalendarCheck, faUsers, faTachometerAlt, faHeadset, faRightFromBracket, faHourglass, faHourglassHalf, faChevronCircleDown, faCheckCircle, faUserPlus } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faFacebook, faTwitter, faInstagram, faTiktok, faYoutube } from '@fortawesome/free-brands-svg-icons'

// ❗ Add this line to import the reCAPTCHA plugin
import { VueReCaptcha } from 'vue-recaptcha-v3'

library.add(faFacebook,faPhone,faTimesCircle,faCreditCard,faUserCircle, faTwitter, faInstagram, faTiktok, faYoutube,faBars,faTimes, faEdit,faTrash,faPlus,faUserPlus,faFacebook, faTwitter, faInstagram, faTiktok, faYoutube,faCheckCircle, faBell,faHourglassHalf, faEnvelope, faBullhorn,faHourglass, faInbox, faCalendarCheck, faUsers, faTachometerAlt, faHeadset, faRightFromBracket)

const app = createApp(App)
const pinia = createPinia()
app.use(pinia)
app.component('font-awesome-icon', FontAwesomeIcon)
app.use(useToast)
app.use(router)

// ❗ Add this block to configure and use the reCAPTCHA plugin
app.use(VueReCaptcha, {
  siteKey: '6Lc62c0rAAAAAEdv23_172kyl6lR2VRA0BOKewSS', // ⚠️ Replace with your actual Site Key
})

app.mount('#app')