import { defineStore } from "pinia";

export const useRolesStore = defineStore("roles", {
    state: () => ({
        items: [],
        fetching: false,
    }),
    actions: {
        async index() {
            this.fetching = true;
            const { statusText, data } = await this.axios("/roles");
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
            }
        },
    },
});
