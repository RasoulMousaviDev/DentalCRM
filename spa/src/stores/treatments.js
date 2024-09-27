import { defineStore } from "pinia";

export const useTreatmentsStore = defineStore("treatments", {
    state: () => ({
        items: [],
        fetching: true,
    }),
    actions: {
        async index() {
            if (this.items.length === 0) {
                this.fetching = true;
                const { statusText, data } = await this.axios.get(
                    "/treatments"
                );
                this.fetching = false;

                if (statusText === "OK") this.items = data.items;
            }
        },
        async store(form) {
            const res = await this.axios.post("/treatments", form);

            if (res.statusText === "OK") this.items.unshift(res.data);

            return res;
        },
        async update(id, form) {
            const res = await this.axios.patch(`/treatments/${id}`, form);

            if (res.statusText === "OK") {
                const index = this.items.findIndex(
                    (item) => item.id === id
                );
                this.items[index] = res.data;
            }

            return res;
        },
        async destroy(id) {
            const res = await this.axios.delete(`/treatments/${id}`);

            if (res.statusText === "OK") {
                const index = this.items.findIndex(
                    (item) => item.id === id
                );
                this.items.splice(index, 1);
            }

            return res;
        },
    },
});
