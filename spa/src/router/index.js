import { useCookie } from "@/composables/cookie";
import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            name: "Login",
            path: "/auth/login",
            component: () => import("@/views/auth/Login.vue"),
        },
        {
            name: "ForgotPassword",
            path: "/password/forgot",
            component: () => import("@/views/auth/ForgotPassword.vue"),
        },
        {
            name: "Panel",
            path: "/",
            component: () => import("@/layouts/Panel.vue"),
            children: [
                {
                    name: "Dashboard",
                    path: "/",
                    component: () => import("@/views/Dashboard.vue"),
                },
                {
                    name: "Users",
                    path: "/users",
                    component: () => import("@/views/Users.vue"),
                },
                {
                    name: "Patients",
                    path: "/patients",
                    component: () => import("@/views/Patients.vue"),
                },
                {
                    name: "PatientDetails",
                    path: "/patient-details/:id",
                    component: () => import("@/views/PatientDetails.vue"),
                },
                {
                    name: "Appointments",
                    path: "/appointments",
                    component: () => import("@/views/Appointments.vue"),
                },
                {
                    name: "Deposits",
                    path: "/deposits",
                    component: () => import("@/views/Deposits.vue"),
                },
            ],
        },
    ],
});

router.beforeEach((to, from, next) => {
    const cookie = useCookie();
    const token = cookie.get("token");

    if (token && (to.path === "/auth/login" || to.path === "/password/forgot"))
        return next("/");
    else if (
        !token &&
        !(to.path === "/auth/login" || to.path === "/password/forgot")
    )
        return next("/auth/login");

    next();
});

export default router;
