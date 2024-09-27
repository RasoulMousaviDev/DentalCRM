<script setup>
import AppMenuItem from './AppMenuItem.vue';
import { ref, inject } from 'vue';

const { t } = inject('service')

const model = ref([
    {
        label: t('user-menu'),
        items: [
            { label: t('dashboard'), icon: 'pi pi-fw pi-home', to: '/' },
            { label: t('users'), icon: 'pi pi-fw pi-id-card', to: '/users' },
            { label: t('patients'), icon: 'pi pi-fw pi-id-card', to: '/patients' },
            { label: t('appointments'), icon: 'pi pi-fw pi-id-card', to: '/appointments' },
            { label: t('deposits'), icon: 'pi pi-fw pi-id-card', to: '/deposits' },
            { label: t('campains'), icon: 'pi pi-fw pi-id-card', to: '/campains' },
            { label: t('survays'), icon: 'pi pi-fw pi-id-card', to: '/survays' },
            { label: t('treatment-plans'), icon: 'pi pi-fw pi-id-card', to: '/treatment-plans' },
            { label: t('sms-templates'), icon: 'pi pi-fw pi-id-card', to: '/sms-templates' },
            { label: t('treatments'), icon: 'pi pi-fw pi-id-card', to: '/treatments' },
            { label: t('follow-ups'), icon: 'pi pi-fw pi-id-card', to: '/follow-ups' },
            { label: t('calls'), icon: 'pi pi-fw pi-id-card', to: '/calls' },
        ]
    },
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <AppMenuItem v-if="!item.separator" :item="item" :index="i" />
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
