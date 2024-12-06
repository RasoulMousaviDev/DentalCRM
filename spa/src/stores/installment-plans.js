import { defineStore } from "pinia";

export const useInstallmentPlansStore = defineStore("installment-plans", {
    state: () => ({
        items: [],
        fetching: true,
    }),
    actions: {
        async index() {
            this.fetching = true;
            const { statusText, data } = await this.axios.get("/installment-plans");
            this.fetching = false;

            if (statusText === "OK") this.items = data.items;
        },
        async store(form) {
            const res = await this.axios.post("/installment-plans", form);

            if (res.statusText === "OK") this.items.unshift(res.data);

            return res;
        },
        async update(id, form) {
            const res = await this.axios.patch(`/installment-plans/${id}`, form);

            if (res.statusText === "OK") {
                const index = this.items.findIndex((item) => item.id === id);
                this.items[index] = res.data;
            }

            return res;
        },
        async destroy(id) {
            const res = await this.axios.delete(`/installment-plans/${id}`);

            if (res.statusText === "OK") {
                const index = this.items.findIndex((item) => item.id === id);
                this.items.splice(index, 1);
            }

            return res;
        },
    },
});
