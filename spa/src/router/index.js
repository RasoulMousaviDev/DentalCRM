import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
      {
        name: 'Login',
        path: '/auth/login',
        component: () => import('../views/auth/Login.vue')
      },
      {
        name: 'ForgotPassword',
        path: '/password/forgot',
        component: () => import('../views/auth/ForgotPassword.vue')
      }
    ],
});

export default router;
