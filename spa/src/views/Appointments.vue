<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" removable-sort>
            <template #header>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold">{{ $t('appointments') }}</span>
                        <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                        <hr class="grow !ml-2">
                        </hr>
                        <Button icon="pi pi-plus" :label="$t('new-appointment')" severity="success" class="w-32"
                            @click="create()" />
                    </div>
                    <AppointmentFilters />
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
            <template v-if="$route.name != 'Patient'">
                <Column :field="({ patient: { firstname, lastname } }) => [firstname, lastname].join(' ')"
                    :header="$t('patient-name')" />
                <Column v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)" field="patient.user.name"
                    :header="$t('consultant')" />
            </template>
            <Column :field="({ treatments }) => treatments.map(({ title }) => title).join(' | ')"
                :header="$t('treatments')" />
            <Column field="due_date" :header="$t('appointment-date')" bodyClass="ltr" class="w-44" />
            <Column :field="({ deposit = 0 }) => [new Intl.NumberFormat().format(deposit), $t('toman')].join(' ')"
                :header="$t('deposit')" class="w-36" />
            <Column field="status" :header="$t('status')">
                <template #body="{ data: { status } }">
                    <Tag v-bind="status" />
                </template>
            </Column>
            <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
            <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />
            <Column :header="$t('actions')" headerClass="[&>div]:justify-center w-44" body-class="!pl-0">
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <SplitButton v-if="data.status.id == 13" :label="$t('was-visit')" size="small"
                            class="w-32 first:*:grow" :model="getMenu(data)" :loading="data.loading"
                            @click="visit(data)" />
                        <Button v-else-if="[4, 5].includes(data.status.id)" :label="$t('set-deposit')" ize="small"
                            icon="pi pi-credit-card" severity="secondary" class="w-32" :loading="data.loading"
                            @click="deposit(data)" />
                        <Button v-else-if="data.status.id == 6" :label="$t('refund')" ize="small" outlined
                            icon="pi pi-credit-card" severity="danger" class="w-32" :loading="data.loading"
                            @click="refund(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import AppointmentFilters from '@/components/AppointmentFilters.vue';
import AppointmentForm from '@/components/AppointmentForm.vue';
import DepositForm from '@/components/DepositForm.vue';
import { useAppointmentsStore } from '@/stores/appointments';
import { useAuthStore } from '@/stores/auth';
import { inject } from 'vue';

const { route, dialog, confirm, toast, t } = inject('service')

const store = useAppointmentsStore()
if (route.name == 'Patient')
    store.filters.patient = route.params.id
store.index()

const auth = useAuthStore()

const create = async () => {
    dialog.open(AppointmentForm, {
        props: {
            header: t('createNewAppointment'), modal: true
        },
    })
}

const visit = (appointment, status = 4) => {
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

            const { statusText, data } = await store.update(appointment.id, { status });

            appointment.loading = false

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
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

            const { statusText, data } = await store.update(appointment.id, { status: 17 });

            appointment.loading = false

            if (statusText == 'OK') {
                Object.assign(appointment, data)
            }
            else {
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}

const getMenu = (appointment) => ([
    {
        label: t('online-visited'),
        icon: 'pi pi-check',
        command: () => visit(appointment, 5)
    },
    {
        label: t('appointment-cancel'),
        icon: 'pi pi-times',
        command: () => cancel(appointment)
    },
]);

const deposit = (appointment) => {
    dialog.open(DepositForm, {
        props: {
            header: t('createNewDeposit'), modal: true
        },
        data: { appointment }
    })
}


const refund = (appointment) => {
    confirm.require({
        message: t('refund-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('was-refunded'),
            severity: 'danger',
        },
        accept: async () => {
            appointment.loading = true

            const { statusText, data } = await store.update(appointment.id, { status: 15 });

            appointment.loading = false

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}


</script>

<style lang="scss"></style>