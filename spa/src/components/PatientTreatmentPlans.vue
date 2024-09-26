<template>
    <DataTable v-model:expandedRows="expandedRows" :value="store.items" tableStyle="min-width: 50rem" row-hover>
        <template #header>
            <div class="flex items-center gap-2">
                <IconField class="ml-auto">
                    <InputText v-model="value1" :placeholder="$t('search')" />
                    <InputIcon class="pi pi-search" />
                </IconField>
                <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" />
                <Button icon="pi pi-plus" :label="$t('new-treatment-plan')" severity="success" @click="create()" />
            </div>
        </template>
        <template #empty>
            <p class="text-center text-sm opacity-60">
                {{ store.fetching ? $t('loading') : $t('not-found') }}
            </p>
        </template>
        <template #expansion="{ data: { payment_type, id, status } }">
            <Tabs :value="0" class="px-4 pt-1 pb-0 shadow-inner rounded bg-surface-50/70 relative">
                <TabList v-if="payment_type == 'installments'" class="[&_div]:!bg-transparent z-10">
                    <Tab v-for="(tab, i) in tabs" :key="i" :value="i" v-text="$t(tab)" />
                </TabList>
                <TabPanels class="!px-0 !py-2 !bg-transparent">
                    <TabPanel v-for="(tab, i) in tabs" :key="i" :value="i">
                        <TreatmentPlanDetails v-if="tab == 'details'" v-bind="{ id, status }"
                            :class="{ '[&_.p-datatable-header]:-mt-[3.75rem] [&_.p-datatable-header]:pb-2 [&_.p-datatable-header>div>span]:invisible': payment_type == 'installments' }" />
                        <TreatmentPlanInstallments v-else-if="tab == 'installments'" :id="id" />
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </template>
        <Column expander :header="$t('details')" class="w-4 [&_button]:has-[[aria-expanded='false']]:rotate-180" />
        <Column :header="$t('row')" class="w-20">
            <template #body="{ index }">
                {{ index + 1 }}
            </template>
        </Column>
        <Column :field="({ months }) => months > 0 ? $t('installments-months', { months }) : $t('cash')"
            :header="$t('payment-type')" class="w-44" />
        <Column field="desc" :header="$t('desc')" />
        <Column field="tooths_count" :header="$t('tooths-count')" class="w-44" />
        <Column field="treatments_count" :header="$t('services-count')" class="w-44" />
        <Column :field="({ total_cost }) => [new Intl.NumberFormat().format(total_cost), $t('toman')].join(' ')"
            :header="$t('total-cost')" class="w-44" />
        <Column field="status" :header="$t('status')" class="w-44">
            <template #body="{ data: { status } }">
                <Tag :value="$t(status)" :severity="severities[status]" />
            </template>
        </Column>
        <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
        <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-44">
            <template #body="{ data }">
                <div class="flex gap-2 justify-end">
                    <Button :label="$t('final-submit')" icon="pi pi-check" rounded text severity="info"
                        :loading="data.loading" @click="destroy(data)" />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { useTreatmentPlansStore } from '@/stores/treatment-plans';
import { reactive, inject, ref, defineAsyncComponent, watch } from 'vue';
import TreatmentPlanDetails from './TreatmentPlanDetails.vue';
import TreatmentPlanInstallments from './TreatmentPlanInstallments.vue';

const tabs = reactive(['details', 'installments'])

const severities = reactive({ editing: "info", pending: "warn", done: "success" })

const { dialog, route, t } = inject('service')

const { id } = route.params

const expandedRows = ref([])

const store = useTreatmentPlansStore()
store.index({ patient: id })

const TreatmentPlanForm = defineAsyncComponent(() => import('@/components/TreatmentPlanForm.vue'));

const create = async () => {
    dialog.open(TreatmentPlanForm, {
        props: {
            header: t('createNewTreatmentPlan'), modal: true
        },
    })
}
watch(() => expandedRows.value.length, () => expandedRows.value = expandedRows.value.slice(-1))
</script>

<style lang="scss" scoped></style>