import { defineStore } from "pinia";

export const useUsersStore = defineStore("users", {
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

            const { statusText, data } = await this.axios.get("/users", {
                params: { page, rows, ...this.filters },
            });
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.pagiantor = data.pagiantor;
            }
        },
        async store(form) {
            const res = await this.axios.post("/users", form);

            if (res.statusText === "OK") this.items.unshift(res.data);

            return res;
        },
        async update(id, form) {
            const res = await this.axios.patch(`/users/${id}`, form);

            if (res.statusText === "OK") {
                const index = this.items.findIndex((user) => user.id === id);
                this.items[index] = res.data;
            }

            return res;
        },
        async destroy(id) {
            const res = await this.axios.delete(`/users/${id}`);

            if (res.statusText === "OK") {
                const index = this.items.findIndex((user) => user.id === id);
                this.items.splice(index, 1);
            }

            return res;
        },
        async search(query) {
            this.filters = { query, role: "consultant" };

            await this.index();

            return this.items;
        },
        paginate({ rows, page }) {
            this.pagiantor.rows = rows;
            this.pagiantor.page = page + 1;
            return this.index();
        },
    },
});
