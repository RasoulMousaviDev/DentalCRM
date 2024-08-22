import { defineStore } from "pinia";

export const usePasswordStore = defineStore("password", {
    state: () => ({}),
    actions: {
        async reset(form) {
            return await this.axios.post("/password/reset", form);
        },
        async change(form) {
            return await this.axios.post("/password/change", form);
        },
    },
});
