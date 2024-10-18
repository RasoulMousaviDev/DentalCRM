import { useCookie } from "@/composables/cookie";
import { defineStore } from "pinia";

export const usePasswordStore = defineStore("password", {
    state: () => ({}),
    actions: {
        async reset(form) {
            return await this.axios.post("/password/reset", form);
        },
        async change(form) {
            const res = await this.axios.post("/password/change", form);


            if (res.statusText == "OK") {
                const { token, expires_at } = res.data;

                const cookie = useCookie();
                cookie.set("token", token, expires_at);
            }

            return res;
        },
    },
});
