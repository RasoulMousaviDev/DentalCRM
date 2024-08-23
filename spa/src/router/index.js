import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            name: "Login",
            path: "/auth/login",
            component: () => import("../views/auth/Login.vue"),
        },
        {
            name: "ForgotPassword",
            path: "/password/forgot",
            component: () => import("../views/auth/ForgotPassword.vue"),
        },
        {
            name: "Panel",
            path: "/",
            component: import("@/layouts/Panel.vue"),
            children: [
                {
                    name: "Dashboard",
                    path: "/",
                    component: import("@/views/Dashboard.vue"),
                },
                {
                  name: "Users",
                  path: "/users",
                  component: import("@/views/Users.vue"),
              },
            ],
        },
    ],
});

export default router;
