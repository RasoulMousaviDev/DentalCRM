<script setup>
import AppMenuItem from './AppMenuItem.vue';
import { useAuthStore } from '@/stores/auth';
import { inject, computed } from 'vue';

const auth = useAuthStore()

const { t } = inject('service')

const model = computed(() => [
    {
        title: `${t('user-menu')} (${auth.user.role?.title ?? ''})`,
        items: auth.user.menu
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
