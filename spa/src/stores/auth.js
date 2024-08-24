import { useCookie } from "@/composables/cookie";
import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        token: null,
        expires_at: null,
    }),
    actions: {
        async login(credentials) {
            const res = await this.axios.post("/auth/login", credentials);

            if (res.statusText == "OK") {
                const { token, expires_at } = res.data;

                const cookie = useCookie();
                cookie.set("token", token, expires_at);
            }

            return res;
        },
        refresh() {},
        logout() {},
    },
});
