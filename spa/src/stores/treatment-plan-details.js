import { defineStore } from "pinia";

export const useTreatmentPlanDetailsStore = defineStore(
    "treatment-plan-details",
    {
        state: () => ({
            id: null,
            items: [],
            pagiantor: {},
            fetching: true,
        }),
        actions: {
            async index(page = 1, rows = 10, query = "") {
                this.items = [];
                this.fetching = true;
                const { statusText, data } = await this.axios.get(
                    `/treatment-plans/${this.id}/details`,
                    {
                        params: { page, rows, query },
                    }
                );
                this.fetching = false;

                if (statusText === "OK") {
                    this.items = data.items;
                    this.pagiantor = data.pagiantor;
                }
            },
            async store(form) {
                const res = await this.axios.post(
                    `/treatment-plans/${this.id}/details`,
                    form
                );

                if (res.statusText === "OK") {
                    const items = res.data.reverse();
                    items.forEach((item) => this.items.unshift(item));
                }

                return res;
            },
        },
    }
);
