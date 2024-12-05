import MyDate from "@/utils/MyDate";
import { defineStore } from "pinia";

export const useHolidaysStore = defineStore("holidays", {
    state: () => ({
        items: {},
        fetching: true,
    }),
    actions: {
        async index() {
            this.fetching = true;

            const { statusText, data } = await this.axios.get("/holidays");

            this.fetching = false;

            if (statusText === "OK")
                this.items = data.items.map(({ date }) => {
                    const parts = date.split("-");
                    const year = +parts[0];
                    const month = +parts[1];
                    const day = +parts[2];
                    return new MyDate(new Date(year, month - 1, day).getTime());
                });
        },
    },
});
