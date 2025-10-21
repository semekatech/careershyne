import './assets/main.css'
import 'flowbite'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router/router.js'
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.core.css'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import axios from 'axios' // 👈 add axios import

// Font Awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faBars, faPhone, faBell, faTimes, faEdit, faTrash, faTimesCircle, faUserCircle, faPlus,
  faCreditCard, faEnvelope, faBullhorn, faInbox, faCalendarCheck, faUsers, faTachometerAlt,
  faHeadset, faRightFromBracket, faHourglass, faHourglassHalf, faChevronCircleDown,
  faCheckCircle, faUserPlus, faShoppingCart, faFileAlt, faUserEdit, faSignOutAlt
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faFacebook, faTwitter, faInstagram, faTiktok, faYoutube } from '@fortawesome/free-brands-svg-icons'

import { useAuthStore } from '@/stores/auth' // 👈 import auth store

// Add icons
library.add(
  faFacebook, faPhone, faTimesCircle, faCreditCard, faUserCircle, faTwitter, faInstagram, faTiktok, faYoutube,
  faBars, faTimes, faEdit, faTrash, faPlus, faUserPlus, faCheckCircle, faBell, faHourglassHalf, faEnvelope,
  faBullhorn, faHourglass, faInbox, faCalendarCheck, faUsers, faTachometerAlt, faHeadset, faRightFromBracket,
  faChevronCircleDown, faShoppingCart, faFileAlt, faUserEdit, faSignOutAlt
)

const app = createApp(App)
const pinia = createPinia()
app.use(pinia)
app.component('font-awesome-icon', FontAwesomeIcon)
app.use(useToast)
app.use(router)

// ✅ Auto logout on 401 interceptor
// axios.interceptors.response.use(
//   response => response,
//   error => {
//     if (error.response && error.response.status === 401) {
//       const auth = useAuthStore()
//       auth.clearToken()
//       window.location.href = '/login' 
//     }
//     return Promise.reject(error)
//   }
// )

app.config.globalProperties.$axios = axios

app.mount('#app')
