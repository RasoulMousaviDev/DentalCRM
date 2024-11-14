<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" row-hover editMode="cell"
            class="[&_tbody>tr]:cursor-pointer" @cell-edit-complete="onCellEditComplete" @row-click="onRowClick">
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('treatment-plans') }}
                    </span>
                    <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" @click="popover.show" />
                    <Button icon="pi pi-plus" :label="$t('new-treatment-plan')" severity="success" as="router-link"
                        :to="{ name: 'TreatmentPlanCreate' }" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <Column :field="({ patient: { firstname, lastname } }) => [firstname, lastname].join(' ')"
                :header="$t('patient-name')" class="w-40" />
            <Column :field="({ visit_type }) => $t(visit_type)" :header="$t('visit-type')" class="w-28" />
            <Column field="desc" :header="$t('desc')" body-class="truncate" />
            <Column :field="({ payment_method }) => $t(payment_method)" :header="$t('payment-method')" class="w-36" />
            <Column
                :field="({ deposit_amount }) => [new Intl.NumberFormat().format(deposit_amount), $t('toman')].join(' ')"
                :header="$t('deposit')" class="w-36" />
            <Column :field="({ total_amount }) => [new Intl.NumberFormat().format(total_amount), $t('toman')].join(' ')"
                :header="$t('total-cost')" class="w-36" />
            <Column
                :field="({ discount_amount }) => [new Intl.NumberFormat().format(discount_amount), $t('toman')].join(' ')"
                :header="$t('discount')" class="w-36" />
            <Column field="status" :header="$t('status')" class="w-40">
                <template #body="{ data: { status } }">
                    <Tag :value="$t(status)" :severity="severities[status]" class="cursor-pointer" />
                </template>
                <template #editor="{ data, field }">
                    <Select v-model="data[field]" option-value="value" focusOnHover
                        :options="Object.entries(severities).map(([value, severity]) => ({ value, severity }))" fluid
                        checkmark :placeholder="$t('choose')" class="has-[+small]:!border-red-500 -my-2">
                        <template #value="{ value }">
                            <Tag :value="$t(value)" :severity="severities[value]" />
                        </template>
                        <template #option="{ option }">
                            <Tag :value="$t(option.value)" :severity="option.severity" />
                        </template>
                    </Select>
                </template>
            </Column>
            <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
            <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />
        </DataTable>
    </div>
</template>

<script setup>
import { useTreatmentPlansStore } from '@/stores/treatment-plans';
import { reactive, inject, defineAsyncComponent } from 'vue';

const severities = reactive({ valid: "info", invalid: "warn", done: "success" })

const { toast, popover, router } = inject('service')

popover.value.component = defineAsyncComponent(() => import('@/components/TreatmentPlanFilters.vue'));

const store = useTreatmentPlansStore()
store.index()

const onCellEditComplete = async (event) => {
    let { data: { id }, newValue, index, value } = event;

    if (newValue != value) {
        store.items[index].status = newValue;

        const { statusText, data } = await store.update(id, { status: newValue });

        if (statusText == 'OK')
            toast.add({ severity: 'success', summary: 'Success', detail: 'Status updated', life: 3000 });
        else {
            toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            store.items[index].status = value;
        }
    }
};


const onRowClick = (e) => {
    if (e.originalEvent.target.className != 'p-tag-label') {
        router.push({ name: 'TreatmentPlan', params: { id: e.data.id } })
    }
}
</script>

<style lang="scss" scoped></style>