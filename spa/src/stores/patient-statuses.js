import { defineStore } from "pinia";

export const usePatientStatuesStore = defineStore("patient-statuses", {
    state: () => ({
        items: [],
        fetching: true,
    }),
    actions: {
        async index() {
            if (this.items.length === 0) {
                this.fetching = true;
                const { statusText, data } = await this.axios.get(
                    "/patient-statuses"
                );
                this.fetching = false;

                if (statusText === "OK") this.items = data.items;
            }
        },
    },
});
