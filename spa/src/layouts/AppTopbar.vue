<script setup>
import MyDate from '../utils/MyDate'
import AutoComplete from 'primevue/autocomplete';
import AppConfigurator from './AppConfigurator.vue';
import IconSikai from '@/components/icon/Sakai.vue'
import { useCookie } from '@/composables/cookie';
import { useLayout } from '@/composables/layout';
import { useAuthStore } from '@/stores/auth';
import { useAlarmsStore } from '@/stores/alarms';
import { useRolesStore } from '@/stores/roles';
import { usePatientsStore } from '@/stores/patients';
import { defineAsyncComponent, inject, ref, watch, computed, onMounted } from 'vue';

const ChangePasswordForm = defineAsyncComponent(() => import('@/components/ChangePasswordForm.vue'));

const { onMenuToggle, toggleDarkMode, isDarkTheme } = useLayout();

const { router, dialog, t, confirm, route } = inject('service')

const auth = useAuthStore()

const roles = useRolesStore()

const menu = ref();

const items = computed(() => {
    const items = [];

    if (auth.user.roles?.length > 1)
        items.push({
            label: t('change-role'),
            icon: 'pi pi-user',
            items: auth.user.roles.map((id) => {
                const { title: label } = roles.items.find(role => role.id == id) || {}
                const icon = '' + (id == auth.user.role.id ? 'pi pi-check' : 'w-4')
                return {
                    label, icon, command: async () => {
                        await auth.changeRole(id);
                        const otpion = auth.user.menu[0]
                        if (otpion.route == route.path)
                            location.reload()
                        else router.replace(otpion.route)
                    }
                }
            })
        });

    items.push(
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
        })

    return items;
});

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

const handleClick = (name) => {
    if (name == 'periodic-visits')
        router.push('/appointments')
    else if (name == 'appointments')
        router.push('/appointments')
    else if (name == 'follow-ups')
        router.push('/follow-ups?status=pending')
    else if (name == 'follow-up-blacklogs')
        router.push('/follow-ups?status=lost')

}

const alarms = useAlarmsStore()
alarms.index()
onMounted(() => setTimeout(() => alarms.index(), 3000))

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

            <div class="flex gap-4 [&_.p-button-label]:hidden">
                <Button v-if="['reception', 'phone-consultant'].includes(auth.user?.role?.name)" icon="pi pi-calendar"
                    rounded outlined :badge="`${alarms.items.appointments || 0}`" badgeSeverity="success"
                    v-tooltip.bottom="$t('appointments')" @click="handleClick('appointments')" />
                <Button v-if="['reception', 'on-site-consultant'].includes(auth.user?.role?.name)" icon="pi pi-book"
                    rounded outlined :badge="`${alarms.items.visits || 0}`" badgeSeverity="success"
                    v-tooltip.bottom="$t('visits')" @click="handleClick('appointments')" />
                <template v-if="auth.user?.role?.name == 'phone-consultant'">
                    <Button icon="pi pi-calendar-times" rounded outlined :badge="`${alarms.items.periodic_visits || 0}`"
                        badgeSeverity="success" v-tooltip.bottom="$t('periodic-visits')"
                        @click="handleClick('periodic-visits')" />
                    <Button icon="pi pi-list-check" rounded outlined :badge="`${alarms.items.follow_ups || 0}`"
                        badgeSeverity="success" v-tooltip.bottom="$t('follow-ups')"
                        @click="handleClick('follow-ups')" />
                    <Button icon="pi pi-book" rounded outlined :badge="`${alarms.items.follow_up_backlogs || 0}`"
                        badgeSeverity="success" v-tooltip.bottom="$t('follow-up-blacklogs')"
                        @click="handleClick('follow-up-blacklogs')" />
                </template>

            </div>
            <span class="w-px h-6 my-auto bg-current"></span>
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
            <Button icon="pi pi-user" text rounded @click="toggle" />
            <TieredMenu ref="menu" :model="items" popup class="[&_span]:last:[&>*>li]:text-red-500">
                <template #start>
                    <div class="px-4 py-2">
                        {{ auth.user.name }}
                    </div>
                </template>
            </TieredMenu>
        </div>
    </div>
</template>
