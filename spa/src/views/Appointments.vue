<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" removable-sort>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('appointments') }}
                    </span>
                    <IconField>
                        <InputText v-model="store.filters.query" :placeholder="$t('search')" />
                        <InputIcon :class="`pi pi-${store.fetching ? 'spin pi-spinner' : 'search'}`" />
                    </IconField>
                    <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" @click="popover.show" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <template #footer>
                <Paginator v-if="store.pagiantor.totalRecords" v-bind="store.pagiantor" @page="store.paginate" />
            </template>
            <Column :header="$t('row')" class="w-20">
                <template #body="{ index }">
                    {{ index + 1 }}
                </template>
            </Column>
            <Column :field="({ patient: { firstname, lastname } }) => [firstname, lastname].join(' ')"
                :header="$t('patient-name')" />
            <Column :field="({ treatment: { title } }) => title" :header="$t('treatment')" class="w-44" />
            <Column field="due_date" :header="$t('appointment-date')" bodyClass="ltr" class="w-44" />
            <Column field="desc" :header="$t('desc')" />
            <Column field="status" :header="$t('status')" class="w-44">
                <template #body="{ data: { status } }">
                    <Tag :value="$t(status)" :severity="severities[status]" />
                </template>
            </Column>
            <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
                <template #body="{ data }">
                    <div class="flex gap-2 pr-2">
                        <Button v-if="data.status == 'visited'" size="large" icon="pi pi-credit-card" rounded
                            severity="secondary" :loading="data.loading" @click="deposit(data)" />
                        <Button v-else icon="pi pi-check" size="small" rounded severity="success"
                            :loading="data.loading" @click="visit(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import { useAppointmentsStore } from '@/stores/appointments';
import { defineAsyncComponent, inject, reactive, watch } from 'vue';

const DepositForm = defineAsyncComponent(() => import('@/components/DepositForm.vue'));

const severities = reactive({ pending: "warn", visited: "success", missed: "danger", canceled: 'info' })

const { dialog, confirm, toast, popover, t } = inject('service')

popover.value.component = defineAsyncComponent(() => import('@/components/AppointmentFilters.vue'));

const store = useAppointmentsStore()

if (store.items.length === 0)
    store.index()

const visit = (appointment) => {
    confirm.require({
        message: t('visit-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('was-visited'),
            icon: 'pi pi-check',
            severity: 'success',
        },
        accept: async () => {
            appointment.loading = true

            const { statusText, data } = await store.update(appointment.id, { status: 'visited' });

            appointment.loading = false

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}

const deposit = (appointment) => {
    dialog.open(DepositForm, {
        props: {
            header: t('createNewDeposit'), modal: true
        },
        data: { appointment }
    })
}

let timer;
watch(() => store.filters.query, (v) => {
    if (v != undefined) {
        clearTimeout(timer)
        timer = setTimeout(() => {
            if (v) store.filters = { query: v }
            else delete store.filters.query
            store.index()
        }, 300);
    }
})
</script>

<style lang="scss"></style>