<template>
    <div v-if="(!readonly || !loading) && !treatments.fetching" class="max-w-full flex gap-8 relative">
        <div class="grow max-w-[calc(100%_-_24rem)] flex flex-col gap-8">
            <div class="card !p-4 flex gap-2 items-center">
                <Button icon="pi pi-chevron-right" text rounded @click="() => treatRef.scrollTo(0, 0)" />
                <div ref="treatRef" class="w-full overflow-x-auto [&::-webkit-scrollbar]:hidden scroll-smooth">
                    <ul class="flex gap-4 p-2">
                        <li v-for="(item, i) in treatments.items" :key="i" class="shrink-0">
                            <Button :label="item.title" :severity="currentTab == item.id ? 'primary' : 'secondary'"
                                :badge="form.treatments_details[`${item.id}`]?.tooths.length || null"
                                @click="selectTreatment(item.id)" />
                        </li>
                    </ul>
                </div>
                <Button icon="pi pi-chevron-left" text rounded @click="() => treatRef.scrollTo(-1000, 0)" />
            </div>
            <div class="card flex flex-col justify-center items-center border border-transparent"
                :class="{ '!border-red-500': errors[`treatments_details.${currentTab}.tooths`] }">
                <template v-if="currentTab">
                    <SelectTooth v-model="form.treatments_details[`${currentTab}`].tooths" class="h-[30rem]"
                        :disabled="readonly" />
                    <small v-text="errors[`treatments_details.${currentTab}.tooths`]?.[0]" class="text-red-500" />
                </template>

                <div v-else class="flex justify-center items-center h-60">
                    <Message :severity="errors.treatments_details ? 'error' : 'secondary'" class="px-8">
                        {{ $t('select-treatment') }}
                    </Message>
                </div>
            </div>

            <template v-if="form.treatments_details[`${currentTab}`]?.tooths.length > 0 && !readonly">
                <div v-if="getTreatment(currentTab, 'services')?.length > 0" class="card">
                    <p class="text-lg font-bold flex gap-4">
                        <span>{{ $t('required-services') }} </span>
                        <hr class="grow">
                    </p>
                    <ul class="flex flex-wrap gap-6">
                        <li v-for="(service, i) in getTreatment(currentTab, 'services')" :key="i">
                            <div class="flex flex-col gap-2 flex-1 max-w-80 min-w-72">
                                <label>{{ $t(service.title) }}</label>
                                <Select v-model="form.treatments_details[currentTab].services[`${service.id}`]"
                                    :options="service.options" optionLabel="title" optionValue="id" fluid
                                    :placeholder="$t('choose')" show-clear :readonly="readonly">
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

                <div v-if="form.payment_method == 'installments'" class="card border border-transparent"
                    :class="{ '!border-red-500': errors.months_count }">
                    <DataTable :value="rows"
                        class="last:[&_tr]:!bg-[var(--surface-ground)] [&_th]:!bg-[var(--surface-ground)] ">

                        <Column field="title" :header="$t('installment-terms')" :footer="$t('choice')"
                            footer-class="!text-right" />
                        <Column v-for="(item, i) in installments" :key="i" :field="item"
                            :header="`${item} ${$t('months')} `" class="!text-center [&_*]:justify-center">
                            <template #footer>
                                <div class="flex justify-center ">
                                    <Button icon="pi pi-check" rounded :disabled="readonly"
                                        :severity="form.months_count != item ? 'secondary' : null"
                                        @click="showChecks(item)" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <div v-if="form.months_count" class="card flex flex-col gap-6 border border-transparent"
                    :class="{ '!border-red-500': errors.checks_count }">
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold ml-auto">
                            {{ $t('calculation-head-check') }}
                        </span>
                        <InputGroup class="ltr !w-[27.5rem]">
                            <DatePicker v-model="form.start_date" inputClass="ltr" panelClass="ltr"
                                dateFormat="yy/mm/dd" :min-date="new Date()" :readonly="readonly" />
                            <InputGroupAddon>{{ $t('treatment-start-date') }}</InputGroupAddon>
                        </InputGroup>
                    </div>
                    <DataTable :value="checks" class="[&_th]:!bg-[var(--surface-ground)]">
                        <Column :header="$t('nuber-of-checks')" field="count" />
                        <Column :header="$t('amount-of-each-check')" field="amount" />
                        <Column :header="$t('date-of-each-check')" header-class="*:justify-center" class="w-2/3">
                            <template #body="{ data: { dates =[] } }">
                                <ul class="flex flex-wrap justify-center gap-2">
                                    <li v-for="(date, i) in dates" :key="i">
                                        <Tag severity="secondary" :value="date" />
                                    </li>
                                </ul>
                            </template>
                        </Column>
                        <Column :header="$t('remaining')" field="remaining" />
                        <Column :header="$t('choice')" class="w-16 !text-center">
                            <template #body="{ data: { count } }">
                                <Button icon="pi pi-check" rounded :disabled="readonly"
                                    :severity="form.checks_count != count ? 'secondary' : null"
                                    @click="form.checks_count = count" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </template>
        </div>

        <div class="w-96 shrink-0">
            <div class="sticky top-24 flex flex-col gap-8">
                <div class="card !p-6 flex flex-col gap-5 [&_small]:-mb-6">
                    <div class="flex justify-between items-center border-b pb-4">
                        <span class="text-lg font-bold">
                            {{ $t('visit-type') }}
                        </span>
                        <SelectButton v-model="form.visit_type" :options="['online', 'in-person']"
                            :optionLabel="(item) => $t(item)" class="ltr" :disabled="readonly" />
                    </div>
                    <div class="flex flex-col gap-1 col-span-2 mb-2">
                        <FloatLabel variant="on">
                            <Select v-model="form.patient" :options="patients.items" option-value="id"
                                :loading="patients.fetching" resetFilterOnHide autoFilterFocus filter
                                :filterFields="[({ mobiles }) => mobiles.map(m => m.number).join(' ')]"
                                :optionLabel="({ firstname, lastname }) => [firstname, lastname].join(' ')" fluid
                                :invalid="errors.patient" :disabled="disabled"
                                panel-class="[&_.p-iconfield]:ltr [&_input:not(.p-filled)]:!text-right"
                                :filter-placeholder="$t('search-patient')" @filter="patients.search($event.value)">
                            </Select>
                            <label> {{ $t('patient') }}</label>
                        </FloatLabel>
                        <small v-if="errors.patient" v-text="errors.patient[0]" class="text-red-500" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <FloatLabel variant="on">
                            <Textarea v-model="form.desc" fluid rows="5" cols="30" :readonly="readonly" />
                            <label> {{ $t('desc') }}</label>
                        </FloatLabel>
                    </div>
                </div>
                <div class="card !p-6 flex-col gap-6 hidden has-[li]:flex">
                    <div class="flex justify-between items-center border-b pb-4">
                        <span class="text-lg font-bold">
                            {{ $t('requested-services') }}
                        </span>
                    </div>
                    <ul class="flex flex-col gap-4">
                        <template v-for="(treatment, key) in form.treatments_details" :key="key">
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
            :optionLabel="(item) => $t(item)" class="ltr" :disabled="readonly" />
    </div>

    <ul v-if="form.payment_method == 'installments' && form.months_count && readonly" class="flex flex-col gap-6 px-1 mb-2">
        <li v-for="(row, i) in rows.slice(0, 3)" class="flex justify-between items-center">
            <span class="text-sm opacity-80">{{ row.title }}</span>
            <span>{{ row[form.months_count] }}</span>
        </li>
    </ul>
    <div class="flex flex-col gap-2">
        <InputGroup class="ltr">
            <InputGroupAddon class="!px-4">{{ $t('toman') }}</InputGroupAddon>
            <FloatLabel variant="on" class="rtl">
                <InputNumber v-model="form.discount_amount" fluid class="ltr" :readonly="readonly" />
                <label>{{ $t('discount') }}</label>
            </FloatLabel>
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
<div v-if="!readonly" class="flex gap-3">
    <Button icon="pi pi-refresh" :label="$t('restart')" severity="warn" class="shrink-0" @click="handleReset()" />
    <Button icon="pi pi-save" :label="$t('save-information')" class="grow" severity="success" :loading="loading"
        @click="handleSubmit()" />
</div>
</div>
</div>
</div>
</template>

<script setup>
import SelectTooth from '@/components/SelectTooth.vue';
import { useTreatmentServicesStore } from '@/stores/treatment-services';
import { useTreatmentsStore } from '@/stores/treatments';
import { usePatientsStore } from '@/stores/patients';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';
import { useTreatmentPlansStore } from '@/stores/treatment-plans';

const treatRef = ref()

const { router, route, t, toast } = inject('service')

const { patient } = route.query

const { id } = route.params

const readonly = ref(!!id)

const store = useTreatmentPlansStore()

const patients = usePatientsStore()

const treatments = useTreatmentsStore()
treatments.index()

const services = useTreatmentServicesStore()
services.items = []

const form = reactive({
    treatments_details: {},
    payment_method: 'cash',
    visit_type: 'in-person',
})


const errors = ref({})

const currentTab = ref(null)

const selectTreatment = (id) => {
    Object.entries(form.treatments_details).forEach(([k, v]) => {
        Object.entries(v.services).forEach(([k2, v2]) => v2 || delete v.services[k2])

        if (v.tooths.length === 0 && Object.keys(v.services).length === 0) {
            delete form.treatments_details[k]
            delete errors.value[`treatments_details.${k}.tooths`]
        }
    })

    if (!form.treatments_details.hasOwnProperty(id))
        form.treatments_details[id] = { tooths: [], services: {} }
    currentTab.value = id
}

const installments = reactive([3, 6, 9, 12])

const percents = reactive([10, 15, 20, 25])

const total_amount = computed(() => {
    if (form.hasOwnProperty('total_amount')) return form.total_amount
    else {
        let amount = 0;
        Object.entries(form.treatments_details).forEach(([key, { tooths, services }]) => {
            if (tooths.length > 0) {
                const treatment = treatments.items.find(({ id }) => id == key)
                amount += treatment?.cost * tooths.length
                Object.entries(services).forEach(([k, v]) => {
                    if (v) {
                        const services = treatment.services.find(({ id }) => id == k)
                        const option = services.options.find(({ id }) => id == v)
                        amount += option.cost * tooths.length
                    }
                })
            }
        })

        return amount;
    }

})

const final_amount = computed(() => total_amount.value - (form.discount_amount || 0))

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
        const amount = Math.floor((percents[i] / 100) * final_amount.value)
        list[1][`${item}`] = [new Intl.NumberFormat().format(amount), t('toman')].join(' ')
        const amount2 = (final_amount.value - amount)
        list[2][`${item}`] = [new Intl.NumberFormat().format(Math.floor((amount2 / item) / 1000) * 1000), t('toman')].join(' ')
        list[3][`${item}`] = [new Intl.NumberFormat().format(amount2), t('toman')].join(' ')
    });

    return list
})


watch(currentTab, async (v) => {
    services.treatment = v;
    const treatment = treatments.items.find(({ id }) => id == v)
    if (!treatment.hasOwnProperty('services')) {
        treatment.services = []
        await services.index()
        treatment.services = services.items
    }

})

watch(() => form.treatments_details?.[currentTab.value]?.tooths.length, (v) => {
    if (v < 1) form.treatments_details[currentTab.value].services = {}
})

watch(() => form.payment_method, (v) => {
    if (v == 'cash') {
        delete form.months
        delete form.checks_count
        delete form.start_date
    } else form.start_date = new Date()
})

const getTreatment = (t, k = null) => {
    const treatment = treatments.items.find(({ id }) => id == +t)

    return k ? treatment[k] : treatment
}

const getOption = (t, s, o, k) => {
    const services = getTreatment(t, 'services')
    const service = services.find(({ id }) => id == +s)
    const option = service.options.find(({ id }) => id == +o)

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

const checks = ref([])

const getDivisors = (number) => {
    let divisors = [];
    for (let i = 1; i <= number; i++) {
        if (number % i === 0) {
            divisors.push(i);
        }
    }
    return divisors;
}

const calculateDueDates = (months, count) => {
    let dates = [];
    const d = new Date(form.start_date);
    for (let i = 0; i < count; i++) {
        d.setMonth(d.getMonth() + (months / count));
        dates.push(d.toLocaleDateString('fa-IR', { year: 'numeric', month: '2-digit', day: '2-digit' }));
    }

    return dates;
}

const showChecks = (months) => {
    const divisors = getDivisors(months).slice(-3)
    checks.value = []
    const percent = percents[installments.indexOf(months)]
    divisors.forEach(divisor => {
        const row = {}
        row.count = divisor
        const diff = final_amount.value * (100 - percent) / 100
        const amount = Math.floor((diff / divisor) / 1000) * 1000
        row.amount = [new Intl.NumberFormat().format(amount), t('toman')].join(' ')
        const remaining = diff - (amount * divisor)
        row.remaining = [new Intl.NumberFormat().format(remaining), t('toman')].join(' ')
        row.dates = calculateDueDates(months, divisor);
        checks.value.push(row)
    });

    form.months_count = months
    form.deposit_amount = Math.floor((percent / 100) * final_amount.value)
}

const loading = ref(true)

const treatmentPlans = useTreatmentPlansStore()

const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await treatmentPlans.store(form)

    loading.value = false

    if (statusText === 'OK')
        router.replace({ name: 'TreatmentPlans' })
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}

const handleReset = () => {
    currentTab.value = null
    Object.keys(form).forEach(k => delete form[k])
    Object.assign(form, { treatments_details: {}, payment_method: 'cash', })
}

watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
        if (key == 'treatments_details') {
            Object.entries(value[key]).forEach(([k, v]) => {
                if (v.tooths.length > 0)
                    delete errors.value[`treatments_details.${k}.tooths`]
            })
        }
    })

    form.total_amount = total_amount.value
}, { deep: true })


onMounted(async () => {
    if (readonly.value) {
        const { data } = await store.show(id)
        await patients.show(data.patient)
        Object.assign(form, data)
        currentTab.value = Object.keys(data.treatments_details)[0]
    } else if (patient) {
        const p = patients.items.find(item => item.id == patient)
        if (!p) {
            patients.filters.id = patient
            await patients.index()
            form.patient = +patient
        }
    }

    loading.value = false
})

</script>

<style lang="scss" scoped></style>