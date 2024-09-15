<script setup>
import AppMenuItem from './AppMenuItem.vue';
import { ref } from 'vue';

const model = ref([
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/' }]
    },
    {
        label: 'Users',
        items: [
            { label: 'Users List', icon: 'pi pi-fw pi-id-card', to: '/users' },
            { label: 'Patients List', icon: 'pi pi-fw pi-id-card', to: '/patients' },
            { label: 'Appointments List', icon: 'pi pi-fw pi-id-card', to: '/appointments' },
            { label: 'Deposits List', icon: 'pi pi-fw pi-id-card', to: '/deposits' },
        ]
    },
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <AppMenuItem v-if="!item.separator" :item="item" :index="i"/>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
