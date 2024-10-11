import { defineStore } from "pinia";

export const useDepositsStore = defineStore("deposits", {
    state: () => ({
        items: [],
        fetching: true,
        pagiantor: { totalRecords: 0 },
        filters: {},
    }),
    actions: {
        async index() {
            this.items = [];
            this.fetching = true;
            const { page = 1, rows = 10 } = this.pagiantor;

            const { statusText, data } = await this.axios.get("/deposits", {
                params: { page, rows, ...this.filters },
            });
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.pagiantor = data.pagiantor;
            }
        },
        async store(form) {
            const res = await this.axios.post("/deposits", form);

            if (res.statusText === "OK") this.items.unshift(res.data);

            return res;
        },
        async update(id, form) {
            const res = await this.axios.patch(`/deposits/${id}`, form);

            if (res.statusText === "OK") {
                const index = this.items.findIndex(
                    (deposit) => deposit.id === id
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
