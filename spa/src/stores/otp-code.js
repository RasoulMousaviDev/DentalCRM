import { defineStore } from "pinia";
import { useAuthStore } from "./auth";
import { useCookie } from "@/composables/cookie";

export const useOTPCodeStore = defineStore("otp-code", {
    state: () => ({}),
    actions: {
        async generate(form) {
            return await this.axios.post("/otp-code/generate", form);
        },
        async verify(form) {
            const res = await this.axios.post("/otp-code/verify", form);

            if (res.statusText === "OK") {
                const { token, expires_at } = res.data;

                const cookie = useCookie();
                cookie.set("token", token, expires_at);
            }
            return res;
        },
    },
});
