import { defineStore } from "pinia";

export const useTreatmentServicesStore = defineStore(
    "treatment-services",
    {
        state: () => ({
            items: [],
            fetching: true,
            treatment: null,
        }),
        actions: {
            async index() {
                this.fetching = true;
                const { statusText, data } = await this.axios.get(
                    `/treatments/${this.treatment}/services`
                );
                this.fetching = false;

                if (statusText === "OK") this.items = data.items;
            },
            async store(form) {
                const res = await this.axios.post(
                    `/treatments/${this.treatment}/services`,
                    form
                );

                if (res.statusText === "OK") this.items.unshift(res.data);

                return res;
            },
            async update(id, form) {
                const res = await this.axios.patch(
                    `/treatments/${this.treatment}/services/${id}`,
                    form
                );

                if (res.statusText === "OK") {
                    const index = this.items.findIndex(
                        (item) => item.id === id
                    );
                    this.items[index] = res.data;
                }

                return res;
            },
            async destroy(id) {
                const res = await this.axios.delete(
                    `/treatments/${this.treatment}/services/${id}`
                );

                if (res.statusText === "OK") {
                    const index = this.items.findIndex(
                        (item) => item.id === id
                    );
                    this.items.splice(index, 1);
                }

                return res;
            },
        },
    }
);
