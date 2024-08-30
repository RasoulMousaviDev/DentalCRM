<template>
    <RouterView />
    <DynamicDialog />
    <ConfirmDialog />
    <Toast position="bottom-left" />
</template>

<script setup>
import Toast from 'primevue/toast'
import DynamicDialog from 'primevue/dynamicdialog'
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useDialog } from 'primevue/usedialog'
import { useToast } from 'primevue/usetoast'
import { provide, watch } from 'vue'

const { t } = useI18n()
const toast = useToast()
const route = useRouter()
const router = useRouter()
const dialog = useDialog()
const confirm = useConfirm();

const service = { toast, dialog, confirm, router, route, t }

provide('service', service)

watch(route, () => dialog.close());
</script>

<style lang="scss">
@import "./assets/scss/main.scss";
</style>
