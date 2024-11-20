import { defineStore } from "pinia";

export const useAppointmentsStore = defineStore("appointments", {
    state: () => ({
        items: [],
        statuses: [],
        fetching: true,
        pagiantor: { totalRecords: 0 },
        filters: {},
    }),
    actions: {
        async index() {
            this.items = [];
            this.fetching = true;

            const { page = 1, rows = 10 } = this.pagiantor;

            const { statusText, data } = await this.axios.get("/appointments", {
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
            const res = await this.axios.post("/appointments", form);

            if (res.statusText === "OK") this.items.unshift(res.data);

            return res;
        },
        async update(id, form) {
            const res = await this.axios.patch(`/appointments/${id}`, form);

            if (res.statusText === "OK") {
                const index = this.items.findIndex(
                    (appointment) => appointment.id === id
                );
                this.items[index].status = res.data.status;
            }

            return res;
        },
        paginate({ rows, page }) {
            this.pagiantor.rows = rows;
            this.pagiantor.page = page + 1;
            return this.index();
        },
    },
});
