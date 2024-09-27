<template>
    <DataTable :value="store.items" tableStyle="min-width: 50rem" striped-rows>
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
                {{ store.fetching ? $t('loading') : $t('not-found') }}
            </p>
        </template>
        <Column :header="$t('row')" class="w-20">
            <template #body="{ index }">
                {{ index + 1 }}
            </template>
        </Column>
        <Column :field="({ treatment: { title } }) => title" :header="$t('treatment')" class="w-44" />
        <Column field="due_date" :header="$t('appointment-date')" bodyClass="ltr" class="w-44" />
        <Column field="desc" :header="$t('desc')" />
        <Column field="status" :header="$t('status')" class="w-44">
            <template #body="{ data: { status } }">
                <Tag :value="$t(status)" :severity="severities[status]" />
            </template>
        </Column>
        <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
        <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />
        <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-36">
            <template #body="{ data }">
                <div v-if="data.status == 'pending'" class="flex gap-2 justify-end">
                    <Button icon="pi pi-times" :label="$t('appointment-cancel')" rounded outlined size="small"
                        severity="danger" :loading="data.loading" @click="cancel(data)" />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { useAppointmentsStore } from '@/stores/appointments';
import { defineAsyncComponent, inject, reactive } from 'vue';

const severities = reactive({ pending: "warn", visited: "success", missed: "danger", canceled: 'info' })

const { dialog, confirm, route, toast, t } = inject('service')

const { id } = route.params

const store = useAppointmentsStore()
store.index({ patient: id })

const PatientAppointmentForm = defineAsyncComponent(() => import('@/components/PatientAppointmentForm.vue'));

const create = async () => {
    dialog.open(PatientAppointmentForm, {
        props: {
            header: t('createNewAppointment'), modal: true
        },
    })
}

const cancel = (appointment) => {
    confirm.require({
        message: t('cancel-appointment-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('be-canceled'),
            icon: 'pi pi-trash',
            severity: 'danger',
        },
        accept: async () => {
            appointment.loading = true

            const { statusText, data } = await store.update(appointment.id, { status: 'canceled' });

            appointment.loading = false

            if (statusText == 'OK') {
                Object.assign(appointment, data)
            }
            else {
                toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}
</script>

<style lang="scss" scoped></style>