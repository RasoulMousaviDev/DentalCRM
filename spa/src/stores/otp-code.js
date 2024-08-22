import { defineStore } from "pinia";
import { useAuthStore } from "./auth";

export const useOTPCodeStore = defineStore("otp-code", {
    state: () => ({}),
    actions: {
        async generate(form) {
            return await this.axios.post("/otp-code/generate", form);
        },
        async verify(form) {
            const res = await this.axios.post("/otp-code/verify", form);

            if (res.statusText === "OK") {
                const auth = useAuthStore();
                auth.token = res.data.token;
                auth.expires_at = res.data.expires_at;
            }
            return res;
        },
    },
});
