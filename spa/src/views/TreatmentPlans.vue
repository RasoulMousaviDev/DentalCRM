<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" row-hover editMode="cell"
            class="[&_tbody>tr]:cursor-pointer" @cell-edit-complete="onCellEditComplete" @row-click="onRowClick">
            <template #header>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold">{{ $t('treatment-plans') }}</span>
                        <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                        <hr class="grow !ml-2">
                        </hr>
                        <Button icon="pi pi-plus" :label="$t('new-plan')" severity="success" as="router-link"
                            :to="{ name: 'TreatmentPlanCreate', query: { patient: $route.params.id } }" />
                    </div>
                    <TreatmentPlanFilters />
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
            <template v-if="$route.name != 'Patient'">
                <Column :field="({ patient: { firstname, lastname } }) => [firstname, lastname].join(' ')"
                    :header="$t('patient-name')" />
                <Column v-if="['super-admin', 'admin'].includes(auth.user?.role.name)" field="patient.user.name"
                    :header="$t('consultant')" />
            </template>
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
                    <Tag v-bind="status" />
                </template>
                <template #editor="{ data, field }">
                    <Select v-model="data[field]" option-value="value" focusOnHover
                        :options="Object.entries(severities).map(([value, severity]) => ({ value, severity }))" fluid
                        :placeholder="$t('choose')" class="-my-2">
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
import TreatmentPlanFilters from '@/components/TreatmentPlanFilters.vue';
import { useAuthStore } from '@/stores/auth';
import { useTreatmentPlansStore } from '@/stores/treatment-plans';
import { reactive, inject } from 'vue';

const severities = reactive({ valid: "info", invalid: "warn", done: "success" })

const { toast, router, route } = inject('service')

const store = useTreatmentPlansStore()
if (route.name == 'Patient')
    store.filters.patient = route.params.id
store.index()

const auth = useAuthStore()

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