import { defineStore } from "pinia";

export const useCitiesStore = defineStore("cities", {
    state: () => ({
        items: [],
        fetching: false,
    }),
    actions: {
        async index(province) {
            this.fetching = true;

            const { statusText, data } = await this.axios.get("/cities", {
                params: { province },
            });

            this.fetching = false;

            if (statusText === "OK") this.items = data.items;
        },
    },
});
