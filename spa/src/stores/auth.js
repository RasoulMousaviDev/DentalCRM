import { useCookie } from "@/composables/cookie";
import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: {},
        role: null,
    }),
    actions: {
        async login(credentials) {
            const res = await this.axios.post("/auth/login", credentials);

            if (res.statusText == "OK") {
                const { token, expires_at, user } = res.data;

                const cookie = useCookie();
                cookie.set("token", token, expires_at);
            }

            return res;
        },
        async me() {
            const { statusText, data } = await this.axios.get("/auth/me");

            if (statusText == "OK") {
                this.user = data;
                this.role = data.role.id;
            }

            return data
        },
        refresh() {},
        logout() {},
        async changeRole(id) {
            const { statusText, data } = await this.axios.post(
                "/auth/change-role",
                { id }
            );

            if (statusText == "OK") {
                this.user = data;
                this.role = id;
            }
        },
    },
});
