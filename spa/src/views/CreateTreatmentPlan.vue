<template>
    <div class="max-w-full flex gap-8 relative">
        <div class="grow max-w-[calc(100%_-_24rem)] flex flex-col">
            <Tabs v-model:value="currentTab" scrollable class="grow" lazy>
                <TabList class="ltr card !p-4 *:!shadow-none !mb-8" pt:tablist="gap-5 !border-0 px-4 py-2 rtl">
                    <Tab v-for="(item, i) in treatments.items" :key="i" :value="item.id" asChild v-slot="{}">
                        <Button :label="item.title" class="shrink-0"
                            :severity="currentTab == item.id ? 'primary' : 'secondary'"
                            @click="selectTreatment(item.id)"
                            :badge="form.treatments[`${item.id}`]?.tooths.length || null" />
                    </Tab>
                </TabList>
                <TabPanels class="card !mb-8">
                    <TabPanel :value="null">
                        <div class="flex justify-center items-center h-60">
                            <Message severity="secondary" class="px-8">{{ $t('select-treatment') }}</Message>
                        </div>
                    </TabPanel>
                    <TabPanel v-for="(item, i) in treatments.items" :key="i" :value="item.id">
                        <SelectTooth v-model="form.treatments[`${item.id}`].tooths" class="h-[30rem] mx-auto" />
                    </TabPanel>
                </TabPanels>
            </Tabs>

            <template v-if="form.treatments[`${currentTab}`]?.tooths.length > 0">
                <div v-if="subCategories.items.length > 0"
                    class="card">
                    <p class="text-lg font-bold flex gap-4">
                        <span>{{ $t('dedicated-services') }} </span>
                        <hr class="grow">
                    </p>
                    <ul class="flex flex-wrap gap-6">
                        <li v-for="(subCategory, i) in subCategories.items" :key="i">
                            <div class="flex flex-col gap-2 flex-1 max-w-80 min-w-72">
                                <label>{{ $t(subCategory.title) }}</label>
                                <Select v-model="form.treatments[currentTab].services[`${subCategory.id}`]"
                                    :options="subCategory.options" optionLabel="title" optionValue="id" fluid checkmark
                                    :placeholder="$t('choose')" show-clear>
                                    <template #option="{ option: { title, cost } }">
                                        <div class="w-full flex items-center justify-between text-sm">
                                            <span>{{ title }}</span>
                                            <span>{{ new Intl.NumberFormat().format(cost) }}</span>
                                        </div>
                                    </template>
                                </Select>
                            </div>
                        </li>
                    </ul>
                </div>

                <div v-if="form.payment_method == 'installments'" class="card">
                    <DataTable :value="rows"
                        class="last:[&_tr]:!bg-[var(--surface-ground)] [&_th]:!bg-[var(--surface-ground)] ">
                        <template #header>
                            <span class="text-lg font-bold ml-auto">
                                {{ $t('installment-terms') }}
                            </span>
                        </template>
                        <Column field="title" />
                        <Column v-for="(item, i) in installments" :key="i" :field="item"
                            :header="`${item} ${$t('months')} `" class="!text-center [&_*]:justify-center">
                            <template #footer>
                                <div class="flex justify-center ">
                                    <Button :label="$t('choice')" :severity="form.months != item ? 'secondary' : null"
                                        @click="form.months = item" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <div v-if="form.months" class="card flex flex-col gap-6">
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold ml-auto">
                            {{ $t('calculation-head-check') }}
                        </span>
                        <DatePicker v-model="date" class="w-[19rem]" :placeholder="$t('treatment-start-date')"
                            :inputClass="{ 'ltr': date }" panelClass="ltr" dateFormat="yy/mm/dd" showButtonBar
                            @clearClick="date = null" />
                        <Button :label="$t('calculate')" />
                    </div>
                    <DataTable :value="rows2" class="[&_th]:!bg-[var(--surface-ground)]">
                        <Column :header="$t('nuber-of-checks')" field="count" />
                        <Column :header="$t('amount-of-each-check')"
                            :field="({ amount }) => [new Intl.NumberFormat().format(amount), $t('toman')].join(' ')" />
                        <Column :header="$t('date-of-each-check')">
                            <template #body="{ data: { dates =[] } }">
                                <ul class="flex flex-wrap gap-2">
                                    <li v-for="(date, i) in dates" :key="i">
                                        <Tag severity="secondary" :value="date" />
                                    </li>
                                </ul>
                            </template>
                        </Column>
                        <Column :header="$t('remaining')"
                            :field="({ remaining }) => [new Intl.NumberFormat().format(remaining), $t('toman')].join(' ')" />
                        <Column :header="$t('actions')" class="w-16 !text-center">
                            <template #body="{ index }">
                                <Button icon="pi pi-check" rounded :severity="check != index ? 'secondary' : null"
                                    @click="check = index" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </template>

        </div>

        <div class="w-96 shrink-0">
            <div class="sticky top-24">
                <div class="card !p-6  flex-col gap-6 hidden has-[li]:flex">
                    <div class="flex justify-between items-center border-b pb-4">
                        <span class="text-lg font-bold">
                            {{ $t('requested-services') }}
                        </span>
                    </div>
                    <ul class="flex flex-col gap-4">
                        <template v-for="(treatment, key) in form.treatments" :key="key">
                            <li v-if="treatment.tooths.length > 0" class="flex flex-col gap-8">
                                <div class="flex justify-between items-center font-bold">
                                    <span>{{ getTreatment(key, 'title') }}</span>
                                    <span>{{ [new Intl.NumberFormat().format(getTreatment(key, 'cost') *
                                        treatment.tooths.length), $t('toman')].join(' ') }}</span>
                                </div>
                                <ul class="flex flex-col gap-3">
                                    <template v-for="(tooths, i) in getToothsPostion(treatment.tooths)" :key="i">
                                        <li v-if="tooths.length > 0" class="flex items-center gap-2">
                                            <span class="w-20">
                                                {{ [$t(i < 2 ? 'top' : 'bottom'), $t(i % 2 == 0 ? 'left' : 'right'
                                                )].join(' ') }}
                                            </span>
                                            <ul class="flex gap-2">
                                                <li v-for="(number, j) in tooths.sort()" :key="j">
                                                    <Tag severity="secondary" :value="number" />
                                                </li>
                                            </ul>
                                        </li>
                                    </template>
            </ul>

            <ul class="flex flex-col gap-4">
                <li v-for="(option, service) in treatment.services" :key="service">
                    <div class="flex items-center justify-between">
                        <span>{{ getOption(key, service, option, ' title') }}</span>
                                                    <span>{{ [new Intl.NumberFormat().format(getOption(key, service,
                                                        option,
                                                        'cost') *
                                                        treatment.tooths.length), $t('toman')].join(' ') }}</span>
                </div>
                </li>
                </ul>
                </li>
</template>
</ul>
<div
    class="flex justify-between items-center font-bold -mx-6 -mb-6 px-6 py-4 bg-[var(--surface-ground)] rounded-b-md border border-[var(--surface-card)]">
    <span>{{ $t('final-amount') }}</span>
    <span>{{ [new Intl.NumberFormat().format(total_amount), $t('toman')].join(' ') }}</span>
</div>
</div>
<div class="card !p-6 flex flex-col gap-6">
    <div class="flex justify-between items-center border-b pb-4">
        <span class="text-lg font-bold">
            {{ $t('payment-method') }}
        </span>
        <SelectButton v-model="form.payment_method" :options="['installments', 'cash']"
            :optionLabel="(item) => $t(item)" class="ltr" />
    </div>

    <ul v-if="form.payment_method == 'installments' && form.months" class="flex flex-col gap-6">
        <li v-for="(row, i) in rows.slice(0, 3)" class="flex justify-between items-center">
            <span class="text-sm opacity-80">{{ row.title }}</span>
            <span>{{ row[form.months] }}</span>
        </li>
    </ul>
    <div class="flex flex-col gap-2 -mt-2">
        <label>{{ $t('discount') }}</label>
        <InputGroup class="ltr">
            <InputGroupAddon>{{ $t('toman') }}</InputGroupAddon>
            <InputNumber v-model="form.discount_amount" class="w-full" />
        </InputGroup>
    </div>
    <div
        class="flex justify-between items-center font-bold -mx-6 -mb-6 px-6 py-4 bg-[var(--surface-ground)] rounded-b-md">
        <span>{{ $t('amount-payable') }}</span>
        <span>
            {{ [new Intl.NumberFormat().format(final_amount), $t('toman')].join(' ') }}
        </span>
    </div>
</div>
<div class="flex gap-3">
    <Button icon="pi pi-refresh" :label="$t('restart')" severity="warn" class="shrink-0" @click="" />
    <Button icon="pi pi-save" :label="$t('save-information')" class="grow" severity="success" :loading="loading" />
</div>
</div>
</div>
</div>
</template>

<script setup>
import SelectTooth from '@/components/SelectTooth.vue';
import { useTreatmentSubCategoriesStore } from '@/stores/treatment-sub-categories';
import { useTreatmentsStore } from '@/stores/treatments';
import { computed, inject, reactive, ref, watch } from 'vue';

const treatments = useTreatmentsStore()
treatments.index()


const subCategories = useTreatmentSubCategoriesStore()
subCategories.items = []

const form = reactive({
    treatments: {},
    payment_method: 'cash',
    discount_amount: null
})

const currentTab = ref(null)

const selectTreatment = (id) => {
    if (!form.treatments.hasOwnProperty(id))
        form.treatments[id] = { tooths: [], services: {} }
    currentTab.value = id
}

const installments = reactive([3, 6, 9, 12])

const percents = reactive([10, 15, 20, 25])

const total_amount = computed(() => {
    let amount = 0;
    Object.entries(form.treatments).forEach(([key, { tooths, services }]) => {
        if (tooths.length > 0) {
            const treatment = treatments.items.find(({ id }) => id == key)
            amount += treatment.cost * tooths.length
            Object.entries(services).forEach(([k, v]) => {
                if (v) {
                    const subCategories = treatment.subCategories.find(({ id }) => id == k)
                    const option = subCategories.options.find(({ id }) => id == v)
                    amount += option.cost * tooths.length
                }
            })
        }
    })

    return amount;
})

const final_amount = computed(() => total_amount.value - (form.discount_amount || 0))

const { t } = inject('service')

const rows = computed(() => {
    const list = [
        {
            title: `${t('advance-payment')} (${t('percent')})`
        },
        { title: `${t('advance-payment')} (${t('toman')})` },
        { title: t('installment-every-month') },
        { title: t('final-amount') }
    ]

    installments.forEach((item, i) => {
        list[0][`${item}`] = `%${percents[i]}`
        const amount = Math.floor((percents[i] / 100) * total_amount.value)
        list[1][`${item}`] = [new Intl.NumberFormat().format(amount), t('toman')].join(' ')
        const amount2 = (total_amount.value - amount)
        list[2][`${item}`] = [new Intl.NumberFormat().format(Math.floor((amount2 / item) / 1000) * 1000), t('toman')].join(' ')
        list[3][`${item}`] = [new Intl.NumberFormat().format(amount2), t('toman')].join(' ')
    });

    return list
})

const getAmount = () => '1'

const type = ref()
const check = ref()
const date = ref()

const rows2 = reactive([
    { count: 2, amount: 10000000, dates: ['1403/02/02', '1403/03/02'], remaining: 4000 }
])



watch(currentTab, async (v) => {
    subCategories.treatment = v;
    await subCategories.index()
    const treatment = treatments.items.find(({ id }) => id == v)
    treatment.subCategories = subCategories.items
})

watch(() => form.treatments?.[currentTab.value]?.tooths.length, (v) => {
    if (v < 1) form.treatments[currentTab.value].services = {}
})

watch(form.payment_method, (v) => {
    if (v == 'cash') delete form.months
})


const getTreatment = (t, k = null) => {
    const treatment = treatments.items.find(({ id }) => id == +t)

    return k ? treatment[k] : treatment
}

const getOption = (t, s, o, k) => {
    const subCategories = getTreatment(t, 'subCategories')
    const subCategory = subCategories.find(({ id }) => id == +s)
    const option = subCategory.options.find(({ id }) => id == +o)

    return k ? option[k] : option

}

const getToothsPostion = (tooths) => {
    const postions = [[], [], [], []];
    tooths.forEach(tooth => {
        tooth = +tooth
        tooth < 8 && postions[0].push(8 - tooth)
        tooth > 7 && tooth < 15 && postions[1].push(tooth - 7)
        tooth > 14 && tooth < 22 && postions[2].push(22 - tooth)
        tooth > 21 && postions[3].push(tooth - 21)
    });

    return postions;
}

</script>

<style lang="scss" scoped></style>