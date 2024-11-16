<script setup>
import AppConfigurator from './AppConfigurator.vue';
import IconSikai from '@/components/icon/Sakai.vue'
import { useCookie } from '@/composables/cookie';
import { useLayout } from '@/composables/layout';
import { useAuthStore } from '@/stores/auth';
import { useRolesStore } from '@/stores/roles';
import { usePatientsStore } from '@/stores/patients';
import AutoComplete from 'primevue/autocomplete';
import { defineAsyncComponent, inject, ref, watch, computed } from 'vue';

const { onMenuToggle, toggleDarkMode, isDarkTheme } = useLayout();
const { router, dialog, t, confirm } = inject('service')

const ChangePasswordForm = defineAsyncComponent(() => import('@/components/ChangePasswordForm.vue'));
const auth = useAuthStore()
const roles = useRolesStore()
const menu = ref();
const items = ref([
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
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

const query = ref()

const store = usePatientsStore()

const search = ({ query }) => {
    store.filters = { query }
    store.index()
}

const optionSelect = ({ value: { id } }) => {
    delete store.filters.query
    router.push({ name: 'PatientDetails', params: { id } })
}

</script>

<template>
    <div class="layout-topbar shadow-sm">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="onMenuToggle">
                <i class="pi pi-bars"></i>
            </button>
            <router-link to="/" class="layout-topbar-logo">
                <!-- <IconSikai class="" /> -->
                <span class="text-primary">{{ $t('dental-clinic-crm') }}</span>
            </router-link>
        </div>
        <IconField>
            <AutoComplete v-model="query" :suggestions="store.items" forceSelection
                :optionLabel="({ firstname, lastname }) => [firstname, lastname].join(' ')" optionValue="id"
                :placeholder="$t('search-patient')" class="[&_input]:has-[svg]:pl-8" :class="{ 'ltr text-left': query }"
                @complete="search" @optionSelect="optionSelect" />
            <InputIcon class="pi pi-search" />
        </IconField>
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
                    <Button icon="pi pi-user" text rounded @click="toggle" />
                </div>
            </div>
            <Menu ref="menu" :model="items" :popup="true" class="[&_span]:last:[&_li]:text-red-500">
                <template #start>
                    <div class="">
                        <div class="px-4 py-2 text-center">
                            {{ auth.user.name }}
                        </div>
                        <SelectButton v-if="auth.user.roles.length > 1" v-model="auth.role" optionLabel="title" optionValue="id" class="ltr w-full *:flex-1 rounded-none"
                            :options="roles.items.filter(({ id }) => auth.user.roles.includes(id)).reverse()" />
                    </div>

                </template>
            </Menu>
        </div>
    </div>
</template>
