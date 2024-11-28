import { createApp, watch } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";

import PrimeVue from "primevue/config";
// import Aura from "./assets/theme";
import Aura from "@primevue/themes/aura";

import Axios from "axios";

import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Checkbox from "primevue/checkbox";
import InputOtp from "primevue/inputotp";
import Divider from "primevue/divider";
import Message from "primevue/message";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import ColumnGroup from "primevue/columngroup";
import Row from "primevue/row";
import Tag from "primevue/tag";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import Select from "primevue/select";
import MultiSelect from "primevue/multiselect";
import ToggleButton from "primevue/togglebutton";
import Chip from "primevue/chip";
import AutoComplete from "primevue/autocomplete";
import DatePicker from "primevue/datepicker";
import SelectButton from "primevue/selectbutton";
import Textarea from "primevue/textarea";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import Galleria from "primevue/galleria";
import Menu from "primevue/menu";
import InputNumber from "primevue/inputnumber";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import Paginator from "primevue/paginator";
import Listbox from "primevue/listbox";
import Chart from "primevue/chart";

import KeyFilter from 'primevue/keyfilter';
import StyleClass from "primevue/styleclass";
import Ripple from "primevue/ripple";

import ConfirmationService from "primevue/confirmationservice";
import DialogService from "primevue/dialogservice";
import ToastService from "primevue/toastservice";

import { createI18n } from "vue-i18n";
import fa from "./locales/fa.json";
import { useCookie } from "./composables/cookie";
import TieredMenu from "primevue/tieredmenu";
import FloatLabel from "primevue/floatlabel";
import { SplitButton, Tooltip } from "primevue";

const i18n = createI18n({ legacy: false, locale: "fa", messages: { fa } });
const pinia = createPinia();
pinia.use(({ store }) => {
    store.axios = axios;
});

//const baseURL = "http://127.0.0.1:8000/api";
 const baseURL = "https://clinic-crm.chbk.app/api";

const axios = Axios.create({ baseURL });

const app = createApp(App);

app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: { darkModeSelector: ".app-dark" },
    },
    // inputVariant: "filled",
});
app.use(pinia);
app.use(router);
app.use(i18n);
app.use(ToastService);
app.use(DialogService);
app.use(ConfirmationService);

app.component("Button", Button);
app.component("InputText", InputText);
app.component("Password", Password);
app.component("Checkbox", Checkbox);
app.component("InputOtp", InputOtp);
app.component("Divider", Divider);
app.component("Message", Message);
app.component("DataTable", DataTable);
app.component("Column", Column);
app.component("ColumnGroup", ColumnGroup);
app.component("Row", Row);
app.component("Tag", Tag);
app.component("IconField", IconField);
app.component("InputIcon", InputIcon);
app.component("Select", Select);
app.component("MultiSelect", MultiSelect);
app.component("ToggleButton", ToggleButton);
app.component("Chip", Chip);
app.component("AutoComplete", AutoComplete);
app.component("DatePicker", DatePicker);
app.component("SelectButton", SelectButton);
app.component("Textarea", Textarea);
app.component("Tabs", Tabs);
app.component("TabList", TabList);
app.component("Tab", Tab);
app.component("TabPanels", TabPanels);
app.component("TabPanel", TabPanel);
app.component("Galleria", Galleria);
app.component("Menu", Menu);
app.component("InputNumber", InputNumber);
app.component("InputGroup", InputGroup);
app.component("InputGroupAddon", InputGroupAddon);
app.component("Paginator", Paginator);
app.component("Listbox", Listbox);
app.component("Chart", Chart);
app.component("TieredMenu", TieredMenu);
app.component("FloatLabel", FloatLabel);
app.component("SplitButton", SplitButton);

app.directive('keyfilter', KeyFilter);
app.directive("styleclass", StyleClass);
app.directive("ripple", Ripple);
app.directive('tooltip', Tooltip);

app.mount("body");

const cookie = useCookie();
axios.interceptors.request.use((config) => {
    const token = cookie.get("token");
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

axios.interceptors.response.use(
    (res) => {
        if (res.status == 200) res.statusText = "OK";
        return res;
    },
    (err) => {
        if (err?.response?.status === 401) {
            cookie.set("token", "", "Thu, 01 Jan 1970 00:00:01 GMT");
            localStorage.removeItem("state");
            router.push("/auth/login");
        }

        return err.response || {};
    }
);
