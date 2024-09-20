import { defineStore } from "pinia";

export const useSMSTemplatesStore = defineStore("sms-templates", {
    state: () => ({
        items: [],
        pagiantor: {},
        fetching: true,
    }),
    actions: {},
});