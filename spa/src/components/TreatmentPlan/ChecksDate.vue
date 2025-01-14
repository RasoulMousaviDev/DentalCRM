<template>
    <div class="card flex flex-col gap-6 border border-transparent" :class="{ '!border-red-500': errors.months_count }">
        <div class="flex items-center gap-2">
            <span class="text-lg font-bold ml-auto">
                {{ $t("calculation-head-check") }}
            </span>
            <InputGroup class="ltr !w-[27.5rem]">
                <DatePicker v-model="model.start_date" inputClass="ltr" panelClass="ltr" @value-change=""
                    dateFormat="yy/mm/dd" :min-date="new MyDate()" :readonly="readonly" />
                <InputGroupAddon>{{
                    $t("treatment-start-date")
                    }}</InputGroupAddon>
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
            <Column :header="$t('remaining')" field="diff" />
        </DataTable>
    </div>
</template>

<script setup>
import { useInstallmentPlansStore } from '@/stores/installment-plans';
import MyDate from '@/utils/MyDate';
import { formatNumber } from '@/utils/format-number';
import { computed, inject, reactive, readonly, ref, watch } from 'vue';

defineProps({ readonly: { type: Boolean, default: false } })

const { t } = inject('service')

const model = defineModel()

const errors = reactive({})

const plans = useInstallmentPlansStore()

const checks = ref([])

const calculateDueDates = (months) => {
    let dates = [];
    const d = new Date(model.value.start_date);
    for (let i = 0; i < months; i++) {
        d.setMonth(d.getMonth() + 1);
        dates.push(
            d.toLocaleDateString("fa-IR", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            })
        );
    }

    return dates;
};

const generateDates = () => {
    checks.value = [];
    const plan = plans.items.find(({ months_count }) => model.value.months_count == months_count)
    const amount = model.value.final_amount + model.value.final_amount * (plan.interest_percent / 100);
    const deposit = Math.floor((plan.deposit_percent / 100) * amount / 1000) * 1000;

    const row = {};
    row.count = plan.months_count;
    const remaining = amount - deposit;
    const check_amount = Math.floor(remaining / plan.months_count / 1000) * 1000;
    row.amount = [formatNumber(check_amount), t("toman")].join(" ");
    const diff = remaining - check_amount * plan.months_count;
    row.diff = [formatNumber(diff), t("toman")].join(" ");
    row.dates = computed(() => calculateDueDates(plan.months_count));
    checks.value.push(row);
};

watch(model.value, () => generateDates(), { immediate: true })
</script>

<style lang="scss" scoped></style>