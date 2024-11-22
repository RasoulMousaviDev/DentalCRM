import { defineStore } from "pinia";

export const useAlarmsStore = defineStore("alarms", {
    state: () => ({
        items: {},
        fetching: true,
    }),
    actions: {
        async index() {
            this.fetching = true;

            const { statusText, data } = await this.axios.get("/alarms");

            this.fetching = false;

            if (statusText === "OK") this.items = data.items;
        },
    },
});
