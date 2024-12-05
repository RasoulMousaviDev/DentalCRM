<template>
    <div v-if="auth.user" class="layout-wrapper" :class="containerClass">
        <AppTopbar />
        <AppSidebar />
        <div class="layout-main-container">
            <div class="layout-main">
                <RouterView />
            </div>
            <AppFooter />
        </div>
        <div class="layout-mask animate-fadein"></div>
    </div>
</template>

<script setup>
import AppTopbar from './AppTopbar.vue';
import AppSidebar from './AppSidebar.vue';
import AppFooter from './AppFooter.vue';
import { useAuthStore } from '@/stores/auth';
import { useRolesStore } from '@/stores/roles';
import { useHolidaysStore } from '@/stores/holidays';
import { useLayout } from '@/composables/layout';
import { computed, onBeforeMount, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const router = useRouter()
const route = useRoute()

const { layoutConfig, layoutState, isSidebarActive, resetMenu } = useLayout();

const auth = useAuthStore()
auth.me().then(() => {
    if(!auth.user.menu.some((opt) => opt.route == route.path)){
        const otpion = auth.user.menu[0]
        router.replace(otpion.route)
    }
})

const role = useRolesStore()
role.index()

const holidays = useHolidaysStore()
holidays.index()

const outsideClickListener = ref(null);

watch(isSidebarActive, (newVal) => {
    if (newVal) {
        bindOutsideClickListener();
    } else {
        unbindOutsideClickListener();
    }
});

const containerClass = computed(() => {
    return {
        'layout-overlay': layoutConfig.menuMode === 'overlay',
        'layout-static': layoutConfig.menuMode === 'static',
        'layout-static-inactive': layoutState.staticMenuDesktopInactive && layoutConfig.menuMode === 'static',
        'layout-overlay-active': layoutState.overlayMenuActive,
        'layout-mobile-active': layoutState.staticMenuMobileActive
    };
});

function bindOutsideClickListener() {
    if (!outsideClickListener.value) {
        outsideClickListener.value = (event) => {
            if (isOutsideClicked(event)) {
                resetMenu();
            }
        };
        document.addEventListener('click', outsideClickListener.value);
    }
}

function unbindOutsideClickListener() {
    if (outsideClickListener.value) {
        document.removeEventListener('click', outsideClickListener);
        outsideClickListener.value = null;
    }
}

function isOutsideClicked(event) {
    const sidebarEl = document.querySelector('.layout-sidebar');
    const topbarEl = document.querySelector('.layout-menu-button');

    return !(sidebarEl.isSameNode(event.target) || sidebarEl.contains(event.target) || topbarEl.isSameNode(event.target) || topbarEl.contains(event.target));
}


</script>

<style lang="scss" scoped></style>