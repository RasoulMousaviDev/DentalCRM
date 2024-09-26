<template>
    <DataTable :value="store.items" class="[&_table_tr]:!bg-transparent [&_.p-datatable-header]:!bg-transparent"
        tableStyle="min-width: 50rem">
        <template #header>
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold ml-auto">
                    {{ $t('treatment-plan-details') }}
                </span>
                <Button v-if="status == 'editing'" icon="pi pi-plus" class="z-10" :label="$t('new-treatment')"
                    severity="success" @click="create()" />
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
        <Column :field="getPositionTooth" :header="$t('tooth')" />
        <Column field="treatment.title" :header="$t('treatment')" />
        <Column :field="({ cost }) => [new Intl.NumberFormat().format(cost), $t('toman')].join(' ')"
            :header="$t('cost')" class="w-44" />
        <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
        <Column v-if="status == 'editing'" :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
            <template #body="{ data }">
                <Button icon="pi pi-trash" rounded text severity="danger" :loading="data.loading"
                    @click="destroy(data)" />
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { useTreatmentPlanDetailsStore } from '@/stores/treatment-plan-details';
import { defineAsyncComponent, inject, reactive } from 'vue';

const props = defineProps(['id', 'status'])

const { dialog, t } = inject('service')

const store = useTreatmentPlanDetailsStore()
store.id = props.id

if (store.items.length === 0)
    store.index()

const TreatmentPlanDetailForm = defineAsyncComponent(() => import('@/components/TreatmentPlanDetailForm.vue'));

const create = async () => {
    dialog.open(TreatmentPlanDetailForm, {
        props: {
            header: t('createNewTreatment'), modal: true
        },
    })
}

const getPositionTooth = ({ tooth }) => {
    const jaw = tooth < 17 ? 'top' : 'bottom'

    let horizontal;

    if (tooth < 9 || (jaw == 'bottom' && tooth < 25)) horizontal = 'left'
    else if (tooth > 24 || (jaw == 'top' && tooth > 8)) horizontal = 'right'

    if (jaw == 'top')
        tooth = horizontal == 'left' ? 9 - tooth : tooth - 8
    else if (jaw == 'bottom')
        tooth = horizontal == 'left' ? 25 - tooth : tooth - 24

    return [t(`jaw.${jaw}`), t(`jaw.${horizontal}`), tooth].join(' > ')
}
</script>

<style lang="scss"></style>