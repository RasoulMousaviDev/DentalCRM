<template>
    <RouterView />
    <DynamicDialog />
    <Toast />
</template>

<script setup>
import Toast from 'primevue/toast'
import DynamicDialog from 'primevue/dynamicdialog'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useDialog } from 'primevue/usedialog'
import { useToast } from 'primevue/usetoast'
import { provide } from 'vue'

const { t } = useI18n()
const router = useRouter()
const dialog = useDialog()
const toast = useToast()

const service = { toast, dialog, router, t }

provide('service', service)
</script>

<style lang="scss">
@import "./assets/scss/main.scss";
</style>
