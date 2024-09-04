import { defineStore } from "pinia";

export const useCallStatuesStore = defineStore("call-statuses", {
    state: () => ({
        items: [],
        fetching: true,
    }),
    actions: {
        async index() {
            if (this.items.length === 0) {
                this.fetching = true;
                const { statusText, data } = await this.axios.get(
                    "/call-statuses"
                );
                this.fetching = false;

                if (statusText === "OK") this.items = data.items;
            }
        },
    },
});
