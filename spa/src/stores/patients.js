import { defineStore } from "pinia";

export const usePatientsStore = defineStore("patients", {
    state: () => ({
        items: [],
        item: null,
        statuses: [],
        fetching: false,
        pagiantor: { totalRecords: 0 },
        filters: {},
    }),
    actions: {
        async index() {
            this.items = [];
            this.fetching = true;
            const { page = 1, rows = 10 } = this.pagiantor;

            const { statusText, data } = await this.axios.get("/patients", {
                params: { page, rows, ...this.filters },
            });
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.statuses = data.statuses;
                this.pagiantor = data.pagiantor;
            }
        },
        async store(form) {
            const res = await this.axios.post("/patients", form);

            if (res.statusText === "OK") this.items.unshift(res.data);

            return res;
        },
        async show(id) {
            this.fetching = true;

            const res = await this.axios.get(`/patients/${id}`);

            this.fetching = false;

            if (res.statusText === "OK") {
                this.item = res.data;
                this.items.length < 1 && this.items.push(res.data);
            }

            return res;
        },
        async update(id, form) {
            const res = await this.axios.patch(`/patients/${id}`, form);

            if (res.statusText === "OK") {
                const index = this.items.findIndex(
                    (patient) => patient.id === id
                );
                this.items[index] = res.data;
            }

            return res;
        },
        async destroy(id) {
            const res = await this.axios.delete(`/patients/${id}`);

            if (res.statusText === "OK") {
                const index = this.items.findIndex(
                    (patient) => patient.id === id
                );
                this.items.splice(index, 1);
            }

            return res;
        },
        async transfer(form) {
            const res = await this.axios.post("/patients/transfer", form);

            if (res.statusText === "OK") {
                this.items = [];
                this.index();
            }

            return res;
        },
        async export() {
            const page = 1;
            const rows = 100000000;

            const { statusText, data } = await this.axios.get("/patients", {
                params: { page, rows, ...this.filters },
            });

            if (statusText === "OK") return data.items;
        },
        paginate({ rows, page }) {
            this.pagiantor.rows = rows;
            this.pagiantor.page = page + 1;
            return this.index();
        },
        async search(mobile) {
            if (mobile != undefined) {
                this.filters = { mobile };
                await this.index();
            }
            return this.items;
        },
    },
});
