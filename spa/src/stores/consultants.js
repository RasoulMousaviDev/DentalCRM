import { defineStore } from "pinia";

export const useConsultantsStore = defineStore("consultants", {
    state: () => ({
        items: [],
        days: {},
        fetching: true,
    }),
    actions: {
        async index(params) {
            this.fetching = true;

            const { statusText, data } = await this.axios.get("/consultants", {
                params,
            });

            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.days = data.days;
            }
        },
    },
});
