<template>
    <RouterView />
    <DynamicDialog />
    <ConfirmDialog />
    <Toast position="bottom-left" />
    <Popover ref="popover" :dismissable="false">
        <component :is="popover?.component" />
    </Popover>
</template>

<script setup>
import Toast from 'primevue/toast'
import DynamicDialog from 'primevue/dynamicdialog'
import ConfirmDialog from 'primevue/confirmdialog';
import Popover from 'primevue/popover';
import { useConfirm } from "primevue/useconfirm";
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { useDialog } from 'primevue/usedialog'
import { useToast } from 'primevue/usetoast'
import { provide, reactive, ref } from 'vue'

const { t } = useI18n()
const toast = useToast()
const route = useRoute()
const router = useRouter()
const dialog = useDialog()
const confirm = useConfirm();
const popover = ref()

const service = { toast, dialog, confirm, popover, router, route, t }

provide('service', service)

// watch(route, () => dialog?.close());
</script>

<style lang="scss">
@import "./assets/scss/main.scss";
</style>
