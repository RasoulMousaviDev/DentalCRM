<template>
    <div class="max-w-full flex gap-8">
        <div class="grow max-w-[calc(100%_-_24rem)] flex flex-col">
            <Tabs v-model:value="currentTab" scrollable class="grow">
                <TabList class="ltr card !p-4 *:!shadow-none !mb-8" pt:tablist="gap-5 !border-0 px-4 py-2 rtl">
                    <Tab v-for="(item, i) in treatment.items" :key="i" :value="item.id" asChild v-slot="{}">
                        <Button :label="item.title" class="shrink-0"
                            :severity="currentTab == item.id ? 'primary' : 'secondary'" @click="currentTab = item.id"
                            :badge="tooths[item.id] ? tooths[item.id].length : null" />
                    </Tab>
                </TabList>
                <TabPanels class="card !mb-8">
                    <TabPanel :value="null">
                        <div class="flex justify-center items-center h-60">
                            <Message severity="secondary" class="px-8">{{ $t('select-treatment') }}</Message>
                        </div>
                    </TabPanel>
                    <TabPanel v-for="(item, i) in treatment.items" :key="i" :value="item.id">
                        <SelectTooth v-model="tooths[`${item.id}`]" class="h-[30rem] mx-auto" />
                    </TabPanel>
                </TabPanels>
            </Tabs>

            <div class="card">
                <p class="text-lg font-bold flex gap-4">
                    <span>{{ $t('dedicated-services') }} </span>
                    <hr class="grow">
                </p>
                <ul class="flex flex-wrap gap-6">
                    <li v-for="(subCategory, i) in subCategories.items" :key="i">
                        <div class="flex flex-col gap-2 flex-1 max-w-80 min-w-72">
                            <label>{{ $t(subCategory.title) }}</label>
                            <Select v-model="form[subCategory.id]" :options="subCategory.options" optionLabel="title"
                                optionValue="id" fluid checkmark :placeholder="$t('choose')" show-clear>
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

            <div class="card">
                <DataTable :value="rows"
                    class="last:[&_tr]:!bg-[var(--surface-ground)] [&_th]:!bg-[var(--surface-ground)] ">
                    <template #header>
                        <span class="text-lg font-bold ml-auto">
                            {{ $t('installment-terms') }}
                        </span>
                    </template>
                    <Column field="title" />
                    <Column v-for="(item, i) in installments" :key="i" :field="() => getAmount(item, i)"
                        :header="`${item} ${$t('months')} `" class="!text-center [&_*]:justify-center">
                        <template #footer>
                            <div class="flex justify-center ">
                                <Button :label="$t('choice')" :severity="type != item ? 'secondary' : null"
                                    @click="type = item" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>

            <div class="card flex flex-col gap-6">
                <div class="flex items-center gap-2">
                    <span class="text-lg font-bold ml-auto">
                        {{ $t('calculation-head-check') }}
                    </span>
                    <DatePicker v-model="date" class="w-[19rem]" :placeholder="$t('treatment-start-date')"
                        :inputClass="{ 'ltr': date }" panelClass="ltr" dateFormat="yy/mm/dd" showButtonBar
                        @clearClick="date = null" />
                    <Button :label="$t('calculate')" />
                </div>
                <ul class="flex py-3 gap-4 border-y">
                    <li class="flex flex-col gap-2 flex-1">
                        <span class="text-sm opacity-80">{{ $t('payment-method') }}</span>
                        <span>{{ $t('installments-n-months', { n: type }) }}</span>
                    </li>
                    <li v-for="({ title }, i) in rows" class="flex flex-col gap-2 flex-1">
                        <span class="text-sm opacity-80">{{ title }}</span>
                        <span>{{ getAmount(type, i) }}</span>
                    </li>
                </ul>
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
        </div>

        <div class="w-96 shrink-0">
            <div class="card">

            </div>
            <div class="card">
                <span>{{ $t('requested-services') }}</span>
            </div>
            <div class="card">

            </div>
        </div>
    </div>
</template>

<script setup>
import SelectTooth from '@/components/SelectTooth.vue';
import { useTreatmentSubCategoriesStore } from '@/stores/treatment-sub-categories';
import { useTreatmentsStore } from '@/stores/treatments';
import { inject, reactive, ref, watch } from 'vue';

const form = reactive({})

const currentTab = ref(null)

const treatment = useTreatmentsStore()
treatment.index()

const tooths = reactive({})

const subCategories = useTreatmentSubCategoriesStore()
subCategories.items = []

watch(currentTab, (v) => {
    subCategories.treatment = v
    subCategories.index()
})
const { t } = inject('service')

const installments = reactive([3, 6, 9, 12, 18])
const rows = reactive([
    { title: `${t('advance-payment')} (${t('percent')})` },
    { title: `${t('advance-payment')} (${t('toman')})` },
    { title: t('installments-every-month') },
    { title: t('final-amount') },
])

const getAmount = () => '1'

const type = ref()
const check = ref()
const date = ref()

const rows2 = reactive([
    { count: 2, amount: 10000000, dates: ['1403/02/02', '1403/03/02'], remaining: 4000 }
])
</script>

<style lang="scss" scoped></style>