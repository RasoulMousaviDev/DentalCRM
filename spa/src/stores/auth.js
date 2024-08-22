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
                this.token = res.data.token;
                this.expires_at = res.data.expires_at;
            }

            return res;
        },
        refresh() {},
        logout() {},
    },
});
