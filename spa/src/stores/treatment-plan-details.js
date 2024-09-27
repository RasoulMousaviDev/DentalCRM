import { defineStore } from "pinia";
import { useTreatmentPlansStore } from "./treatment-plans";

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
                    const items = res.data.items.reverse();
                    items.forEach((item) => this.items.unshift(item));

                    const store = useTreatmentPlansStore();
                    const treatmentPlan = store.items.find(
                        (item) => item.id == this.id
                    );
                    treatmentPlan.tooths_count += 1;
                    treatmentPlan.treatments_count += 1;
                    treatmentPlan.total_cost = res.data.totalCost;
                }

                return res;
            },
            async destroy(id) {
                const res = await this.axios.delete(
                    `/treatment-plans/${this.id}/details/${id}`
                );

                if (res.statusText === "OK") {
                    const index = this.items.findIndex(
                        (item) => item.id === id
                    );
                    this.items.splice(index, 1);

                    const store = useTreatmentPlansStore();
                    const treatmentPlan = store.items.find(
                        (item) => item.id == this.id
                    );
                    treatmentPlan.tooths_count -= 1;
                    treatmentPlan.treatments_count -= 1;
                    treatmentPlan.total_cost = res.data.totalCost;
                }

                return res;
            },
        },
    }
);
