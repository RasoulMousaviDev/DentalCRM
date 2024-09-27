<template>
    <DataTable :value="items" class="odd:[&_table_tr]:!bg-transparent [&_.p-datatable-header]:!bg-transparent"
        tableStyle="min-width: 50rem">
        <Column :header="$t('row')" class="w-20">
            <template #body="{ index }">
                {{ index + 1 }}
            </template>
        </Column>
        <Column :field="({ amount }) => [new Intl.NumberFormat().format(amount), $t('toman')].join(' ')"
            :header="$t('amount')" />
        <Column field="date" :header="$t('due_date')" class="w-60" />
    </DataTable>
</template>

<script setup>
import { useTreatmentPlansStore } from '@/stores/treatment-plans';
import { computed, reactive } from 'vue';

const props = defineProps(['id'])

const store = useTreatmentPlansStore()

const items = computed(() => {
    const plan = store.items.find((item) => item.id == props.id)
    const totalCost = plan.total_cost
    const months = plan.months
    const amount = Math.ceil(totalCost / months)
    let date = plan.sent_at || Date.parse(plan.created_at) / 1000

    const items = []
    for (let i = 0; i < months; i++) {
        date += 30 * 24 * 60 * 60
        items.push({
            amount,
            date: new Date(date * 1000).toLocaleDateString('fa', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            })
        })
    }

    return items;
})

</script>

<style lang="scss"></style>