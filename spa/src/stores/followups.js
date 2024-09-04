import { defineStore } from "pinia";

export const useFollowupsStore = defineStore("followups", {
    state: () => ({
        items: [],
        pagiantor: {},
        fetching: true,
    }),
    actions: {
        async index({ page = 1, rows = 10, ...query }) {
            this.items = [];
            this.fetching = true;

            const { statusText, data } = await this.axios.get("/followups", {
                params: { page, rows, ...query },
            });
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.pagiantor = data.pagiantor;
            }
        },
    },
});
