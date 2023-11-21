import './bootstrap';

import { createApp } from 'vue';

import App from './components/app.vue'

import router from './router/index.js';
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css';

// import axios from 'axios';
const app = createApp(App)

app.use(router);
app.use(VueSweetalert2);
app.mount("#app");
