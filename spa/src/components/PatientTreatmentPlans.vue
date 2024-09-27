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
                        <TreatmentPlanInstallments v-else-if="tab == 'installments'" :id="id" class="-mt-2" />
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </template>
        <Column expander :header="$t('details')" class="w-4 [&_button]:has-[[aria-expanded='false']]:rotate-180" />
        <Column field="desc" :header="$t('desc')" body-class="truncate" />
        <!-- <Column field="tooths_count" :header="$t('tooths-count')" class="w-32" />
        <Column field="treatments_count" :header="$t('treatments-count')" class="w-32" /> -->
        <Column :field="({ months }) => months > 0 ? $t('installments-months', { months }) : $t('cash')"
            :header="$t('payment-type')" class="w-36" />
        <Column :field="({ deposit }) => [new Intl.NumberFormat().format(deposit), $t('toman')].join(' ')"
            :header="$t('deposit')" class="w-36" />
        <Column :field="({ total_cost }) => [new Intl.NumberFormat().format(total_cost), $t('toman')].join(' ')"
            :header="$t('total-cost')" class="w-36" />
        <Column field="status" :header="$t('status')" class="w-32">
            <template #body="{ data: { status } }">
                <Tag :value="$t(status)" :severity="severities[status]" />
            </template>
        </Column>
        <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
        <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />
        <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
            <template #body="{ data }">
                <div v-if="data.status != 'done'" class="flex justify-end">
                    <template v-if="data.status == 'editing'">
                        <Button icon="pi pi-trash" rounded text severity="danger" :loading="data.deleting"
                            @click="destroy(data)" />
                        <Button icon="pi pi-send" class="scale-x-[-1]" rounded text severity="info"
                            :loading="data.sending" @click="send(data)" />
                    </template>
                    <Button v-else icon="pi pi-check" class="ml-6" rounded text severity="success" :loading="data.doing"
                        @click="done(data)" />
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

const severities = reactive({ editing: "info", sent: "warn", done: "success" })

const { dialog, confirm, toast, route, t } = inject('service')

const { id } = route.params

const expandedRows = ref([])

const store = useTreatmentPlansStore()
store.index({ patient: id })

const TreatmentPlanForm = defineAsyncComponent(() => import('@/components/TreatmentPlanForm.vue'));

const create = async () => {
    expandedRows.value = []
    dialog.open(TreatmentPlanForm, {
        props: {
            header: t('createNewTreatmentPlan'), modal: true
        },
    })
}

const destroy = (plan) => {
    confirm.require({
        message: t('delete-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('delete'),
            icon: 'pi pi-trash',
            severity: 'danger',
        },
        accept: async () => {
            plan.deleting = true

            const { statusText, data } = await store.destroy(plan.id);

            plan.deleting = false

            if (statusText == 'OK')
                toast.add({  severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}

const send = (plan) => {
    confirm.require({
        message: t('send-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('send'),
            icon: 'pi pi-send scale-x-[-1]',
            severity: 'danger',
        },
        accept: async () => {
            plan.sending = true

            const { statusText, data } = await store.update(plan.id, { status: 'sent' });
            console.log(data);

            plan.sending = false

            if (statusText == 'OK')
                toast.add({  severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}

const done = (plan) => {
    confirm.require({
        message: t('done-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('done'),
            icon: 'pi pi-check',
            severity: 'danger',
        },
        accept: async () => {
            plan.doing = true

            const { statusText, data } = await store.update(plan.id, { status: 'done' });

            plan.doing = false

            if (statusText == 'OK')
                toast.add({  severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}

watch(() => expandedRows.value.length, () => expandedRows.value = expandedRows.value.slice(-1))
</script>

<style lang="scss" scoped></style>