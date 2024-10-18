<script setup>
import AppConfigurator from './AppConfigurator.vue';
import IconSikai from '@/components/icon/Sakai.vue'
import { useCookie } from '@/composables/cookie';
import { useLayout } from '@/composables/layout';
import { defineAsyncComponent, inject, ref } from 'vue';

const { onMenuToggle, toggleDarkMode, isDarkTheme } = useLayout();
const { router, dialog, t, confirm } = inject('service')

const ChangePasswordForm = defineAsyncComponent(() => import('@/components/ChangePasswordForm.vue'));

const menu = ref();
const items = ref([
    {
        label: t('user-account'),
        items: [
            {
                label: t('change-password'),
                icon: 'pi pi-key',
                command: () => {
                    dialog.open(ChangePasswordForm, {
                        props: {
                            header: t('change-password'), modal: true
                        },
                    })
                }
            },
            {
                label: t('logout'),
                icon: 'pi pi-sign-out',
                command: () => {
                    confirm.require({
                        message: t('logout-confirm-question'),
                        header: t('danger-zone'),
                        icon: 'pi pi-info-circle',
                        rejectProps: {
                            label: t('back'),
                            severity: 'secondary',
                            outlined: true
                        },
                        acceptProps: {
                            label: t('i-go-out'),
                            icon: 'pi pi-trash',
                            severity: 'danger',
                        },
                        accept: async () => {
                            const cookie = useCookie();
                            cookie.set("token", '', new Date(1970, 0, 0, 0, 0));

                            router.replace({ name: 'Login' })
                        }
                    });
                }
            }
        ]
    }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="onMenuToggle">
                <i class="pi pi-bars"></i>
            </button>
            <router-link to="/" class="layout-topbar-logo">
                <IconSikai class="" />
                <span class="text-primary">{{ $t('dental-clinic-crm') }}</span>
            </router-link>
        </div>

        <div class="layout-topbar-actions">
            <div class="layout-config-menu">
                <div class="relative">
                    <button
                        v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
                        type="button" class="layout-topbar-action ">
                        <i class="pi pi-palette"></i>
                    </button>
                    <AppConfigurator />
                </div>
                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                </button>
            </div>

            <button class="layout-topbar-menu-button layout-topbar-action"
                v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }">
                <i class="pi pi-ellipsis-v"></i>
            </button>
            <div class="layout-topbar-menu hidden lg:block">
                <div class="layout-topbar-menu-content">
                    <!-- <button type="button" class="layout-topbar-action">
                        <i class="pi pi-inbox"></i>
                        <span>Messages</span>
                    </button> -->
                    <button type="button" class="layout-topbar-action" @click="toggle">
                        <i class="pi pi-user"></i>
                        <span>Profile</span>
                    </button>
                </div>
            </div>
            <Menu ref="menu" :model="items" :popup="true" class="[&_span]:last:[&_li]:text-red-500" />
        </div>
    </div>
</template>
