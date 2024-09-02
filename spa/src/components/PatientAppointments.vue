<template>
    <DataTable :value="[]" tableStyle="min-width: 50rem" row-hover>
        <template #header>
            <div class="flex items-center gap-2">
                <IconField class="ml-auto">
                    <InputText v-model="value1" :placeholder="$t('search')" />
                    <InputIcon class="pi pi-search" />
                </IconField>
                <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" />
                <Button icon="pi pi-plus" :label="$t('new-appointment')" severity="success" @click="create()" />
            </div>
        </template>
        <template #empty>
            <p class="text-center text-sm opacity-60">
                <!-- {{ store.fetching ? $t('loading') : $t('not-found') }} -->
            </p>
        </template>
        <Column :field="({ index }) => index + 1" :header="$t('row')" />
        <Column field="treatment" :header="$t('treatment')" />
        <Column field="due_date" :header="$t('appointment-date')" />
        <Column field="status" :header="$t('status')">
            <template #body="{ data: { status } }">
                <Tag :value="$t(status)" :severity="severities[status]" />
            </template>
        </Column>
        <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
        <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />
        <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
            <template #body="{ data }">
                <div class="flex gap-2 justify-end">
                    <Button icon="pi pi-check" :label="$t('appointment-cancel')" rounded outlined severity="danger"
                        @click="cancel(data)" />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { defineAsyncComponent, inject } from 'vue';

const { dialog, t } = inject('service')

const PatientAppointmentForm = defineAsyncComponent(() => import('@/components/PatientAppointmentForm.vue'));

const create = async () => {
    dialog.open(PatientAppointmentForm, {
        props: {
            header: t('createNewAppointment'), modal: true
        },
    })
}
</script>

<style lang="scss" scoped></style>