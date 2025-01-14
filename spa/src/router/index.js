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
                    name: "Patient",
                    path: "/patient/:id",
                    component: () => import("@/views/Patient.vue"),
                },
                {
                    name: "Appointments",
                    path: "/appointments",
                    component: () => import("@/views/Appointments.vue"),
                },
                {
                    name: "Campains",
                    path: "/campains",
                    component: () => import("@/views/Campains.vue"),
                },
                {
                    name: "Survays",
                    path: "/survays",
                    component: () => import("@/views/Survays.vue"),
                },
                {
                    name: "TreatmentPlans",
                    path: "/treatment-plans",
                    component: () => import("@/views/TreatmentPlans.vue"),
                },
                {
                    name: "TreatmentPlanCreate",
                    path: "/treatment-plans/create",
                    alias: "/treatment-plans/:id",
                    component: () => import("@/views/TreatmentPlanCreate2.vue"),
                },
                {
                    name: "TreatmentPlan",
                    path: "/treatment-plans/:id",
                    component: () => import("@/views/TreatmentPlanCreate.vue"),
                },
                {
                    name: "SMSTemplates",
                    path: "/sms-templates",
                    component: () => import("@/views/SMSTemplates.vue"),
                },
                {
                    name: "Treatments",
                    path: "/treatments",
                    component: () => import("@/views/Treatments.vue"),
                },
                {
                    name: "Followups",
                    path: "/follow-ups",
                    component: () => import("@/views/FollowUps.vue"),
                },
                {
                    name: "Calls",
                    path: "/calls",
                    component: () => import("@/views/Calls.vue"),
                },
                {
                    name: "Consultants",
                    path: "/consultants",
                    component: () => import("@/views/Consultants.vue"),
                },
                 {
                    name: "InstallmentPlans",
                    path: "/installment-plans",
                    component: () => import("@/views/InstallmentPlans.vue"),
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
