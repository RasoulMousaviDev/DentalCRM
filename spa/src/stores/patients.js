import { defineStore } from "pinia";

export const usePatientsStore = defineStore("patients", {
    state: () => ({
        items: [],
        pagiantor: {},
        fetching: true,
    }),
    actions: {
        async index(page = 1, rows = 10, query = "") {
            this.items = [];
            this.fetching = true;
            const { statusText, data } = await this.axios.get("/patients", {
                params: { page, rows, query },
            });
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.pagiantor = data.pagiantor;
            }
        },
        async store(form) {
            const res = await this.axios.post("/patients", form);

            if (res.statusText === "OK") 
                this.items.unshift(res.data)

            return res;
        },
        async update(id, form) {
            const res = await this.axios.patch(`/patients/${id}`, form);

            if (res.statusText === "OK") {
               const index = this.items.findIndex((patient) => patient.id === id)
               this.items[index] = res.data
            }

            return res;
        },
        async destroy(id) {
            const res = await this.axios.delete(`/patients/${id}`);

            if (res.statusText === "OK") {
               const index = this.items.findIndex((patient) => patient.id === id)
               this.items.splice(index, 1);
            }

            return res;
        },
    },
});
