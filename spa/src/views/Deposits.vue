<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" removable-sort>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('deposits') }}
                    </span>
                    <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" />
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
            <Column field="appointment.patient.name" :header="$t('patient-name')" class="w-44" />
            <Column :field="({ amount }) => new Intl.NumberFormat().format(amount) + ' ' + $t('toman')"
                :header="$t('amount')" class="w-44" />
            <Column field="payment_date" :header="$t('payment-date')" bodyClass="ltr" class="w-44" />
            <Column :field="({ refund_date }) => refund_date || '---'" :header="$t('refund-date')" bodyClass="ltr"
                class="w-44" />
            <Column field="status" :header="$t('status')" class="w-44">
                <template #body="{ data: { status } }">
                    <Tag :value="$t(status)" :severity="severities[status]" />
                </template>
            </Column>
            <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
                <template #body="{ data }">
                    <div class="flex justify-end">
                        <Button :label="$t('refund')" icon="pi pi-sign-out" outlined size="small" severity="danger"
                            v-show="data.status == 'paid'" :loading="data.loading" @click="refund(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import { useDepositsStore } from '@/stores/deposits';
import { inject, reactive } from 'vue';

const severities = reactive({ paid: "success", refunded: "danger" })

const { confirm, toast, t } = inject('service')

const store = useDepositsStore()

if (store.items.length === 0)
    store.index({})

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

            const { statusText, data } = await store.update(appointment.id, { status: 'refunded' });

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