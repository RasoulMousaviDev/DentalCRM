import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";

import PrimeVue from "primevue/config";
import Aura from "./assets/theme";

import Axios from 'axios'

import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Checkbox from "primevue/checkbox";
import InputOtp from "primevue/inputotp";
import Divider from "primevue/divider";

import StyleClass from 'primevue/styleclass';
import Ripple from 'primevue/ripple'

import DialogService from 'primevue/dialogservice';
import ToastService from 'primevue/toastservice';

import { createI18n } from "vue-i18n";
import fa from './locales/fa.json'

const i18n = createI18n({ legacy: false, locale: 'fa', messages: { fa } })
const pinia = createPinia()
pinia.use(({ store }) => {
    store.axios = axios
})

const baseURL = '/api'

const axios = Axios.create({ baseURL })

const app = createApp(App);

app.use(PrimeVue, { theme: { preset: Aura , options: { darkModeSelector: '.app-dark' }} });
app.use(pinia);
app.use(router);
app.use(i18n)
app.use(DialogService);
app.use(ToastService);

app.component('Button', Button)
app.component('InputText', InputText)
app.component('Password', Password)
app.component('Checkbox', Checkbox)
app.component('InputOtp', InputOtp)
app.component('Divider', Divider)

app.directive('styleclass', StyleClass);
app.directive('ripple', Ripple)

app.mount("body");

watch(pinia.state, (v) => localStorage.setItem('state', JSON.stringify(v)), { deep: true })
pinia.state.value = JSON.parse(localStorage.getItem('state') || '{}')

axios.interceptors.request.use((config) => {
    const token = pinia.state.value.auth.token
    if (token) config.headers.Authorization = `Token ${token}`
    return config
})

// axios.interceptors.response.use(
//     (res) => {},
//     (err) => {}
// )