<template>
    <DataTable :value="[]" tableStyle="min-width: 50rem" row-hover>
        <template #header>
            <div class="flex items-center gap-2">
                <IconField class="ml-auto">
                    <InputText v-model="value1" :placeholder="$t('search')" />
                    <InputIcon class="pi pi-search" />
                </IconField>
                <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" />
                <Button icon="pi pi-plus" :label="$t('new-call')" severity="success" @click="create()" />
            </div>
        </template>
        <template #empty>
            <p class="text-center text-sm opacity-60">
                <!-- {{ store.fetching ? $t('loading') : $t('not-found') }} -->
            </p>
        </template>
        <Column :field="({ index }) => index + 1" :header="$t('row')" />
        <Column field="mobile" :header="$t('mobile')" />
        <Column field="desc" :header="$t('desc')" />
        <Column field="log" :header="$t('log')" />
        <Column field="status" :header="$t('status')">
            <template #body="{ data: { status } }">
                <Tag v-bind="status" />
            </template>
        </Column>
        <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
    </DataTable>
</template>

<script setup>
import { defineAsyncComponent, inject } from 'vue';

const { dialog, t } = inject('service')

const PatientCallForm = defineAsyncComponent(() => import('@/components/PatientCallForm.vue'));

const create = async () => {
    dialog.open(PatientCallForm, {
        props: {
            header: t('createNewCall'), modal: true
        },
    })
}
</script>

<style lang="scss" scoped></style>