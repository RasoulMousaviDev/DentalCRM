import { useCookie } from "@/composables/cookie";
import { inject } from "vue";
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
                {
                    name: "Patients",
                    path: "/patients",
                    component: import("@/views/Patients.vue"),
                },
                {
                    name:'PatientDetails',
                    path: "/patient-details/:id",
                    component: import("@/views/PatientDetails.vue"),
                }
            ],
        },
    ],
});

router.beforeEach((to, from, next) => {
    const cookie = useCookie();
    const token = cookie.get('token');
    
    if (token && (to.path === "/auth/login" || to.path === "/password/forot"))
        return next("/");
    else if (!token && !(to.path === "/auth/login" || to.path === "/password/forgot")){
        localStorage.removeItem('state')
        return next("/auth/login");
    }

    next();
});

export default router;
