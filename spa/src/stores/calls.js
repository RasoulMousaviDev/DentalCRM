import { defineStore } from "pinia";
import { useFollowupsStore } from "./followups";

export const useCallsStore = defineStore("calls", {
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

            const { statusText, data } = await this.axios.get("/calls", {
                params: { page, rows, ...this.filters },
            });
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.pagiantor = data.pagiantor;
            }
        },
        async store(form) {
            const res = await this.axios.post("/calls", form);

            if (res.statusText === "OK") {
                this.items.unshift(res.data.call);

                if (res.data.followup) {
                    const followups = useFollowupsStore();
                    followups.items.unshift(res.data.followup);
                }

                if (form.followup_id) {
                    const followups = useFollowupsStore();
                    const item = followups.items.find(
                        (item) => item.id == form.followup_id
                    );
                    item.status = "done";
                }
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
