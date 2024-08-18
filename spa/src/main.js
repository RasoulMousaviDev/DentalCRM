import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";

import PrimeVue from "primevue/config";
import Aura from "./assets/theme";

import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Password from "primevue/password";

const app = createApp(App);

app.use(PrimeVue, { theme: { preset: Aura , options: { darkModeSelector: 'light2'}} });
app.use(createPinia());
app.use(router);

app.component('Button', Button)
app.component('InputText', InputText)
app.component('Password', Password)

app.mount("body");
