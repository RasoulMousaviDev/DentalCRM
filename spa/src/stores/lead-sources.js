import { defineStore } from "pinia";

export const useLeadSourcesStore = defineStore("lead-sources", {
    state: () => ({
        items: [],
        fetching: true,
    }),
    actions: {
        async index() {
            if (this.items.length === 0) {
                this.fetching = true;

                const { statusText, data } = await this.axios.get(
                    "/lead-sources"
                );
                
                this.fetching = false;

                if (statusText === "OK") this.items = data.items;
            }
        },
    },
});
