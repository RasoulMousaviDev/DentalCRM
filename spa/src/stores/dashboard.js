import { defineStore } from "pinia";

export const useDashboardStore = defineStore("dashboard", {
    state: () => ({
        charts: {},
        fetching: true,
    }),
    actions: {
        async getCharts(params) {
            this.fetching = true;

            const { statusText, data } = await this.axios.get(
                "/dashboard/charts",
                { params }
            );
            this.fetching = false;

            if (statusText === "OK") {
                Object.keys(data).forEach((k) => (this.charts[k] = data[k]));
            }
        },
    },
});
