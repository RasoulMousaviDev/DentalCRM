<template>
    <div class="card !p-6 flex flex-col gap-6 printable">
        <div class="flex justify-between items-center border-b pb-4">
            <span class="text-lg font-bold">
                {{ $t("payment-method") }}
            </span>
            <SelectButton v-model="model.method" :options="['installments', 'cash']" :optionLabel="(item) => $t(item)"
                class="ltr" :disabled="readonly" />
        </div>

        <ul v-if="readonly" class="flex flex-col gap-6 px-1 mb-2">
            <template v-if="model.method == 'installments' && model.months_count">
                <li class="flex justify-between items-center">
                    <span class="text-sm opacity-80">{{ $t("installments") }}</span>
                    <span>{{ model.months_count }} {{ $t('months') }}</span>
                </li>
                <li class="flex justify-between items-center">
                    <span class="text-sm opacity-80">{{ $t("advance-payment") }}</span>
                    <span>{{ [formatNumber(model.deposit_amount), $t("toman"),].join(" ") }}</span>
                </li>
                <li class="flex justify-between items-center">
                    <span class="text-sm opacity-80">{{ $t("amount-of-each-check") }}</span>
                    <span>{{
                        [formatNumber(Math.floor(((model.final_amount - model.deposit_amount) / model.months_count) /
                            1000) * 1000), $t("toman"),].join(" ")
                    }}</span>
                </li>
            </template>

            <li class="flex justify-between items-center">
                <span class="text-sm opacity-80">{{ $t("start-date") }}</span>
                <span>{{ date }}</span>
            </li>

        </ul>
        <div class="flex flex-col gap-2">
            <InputGroup class="ltr">
                <InputGroupAddon class="!px-4"> {{ $t("toman") }}</InputGroupAddon>
                <FloatLabel variant="on" class="rtl">
                    <InputNumber v-model="model.discount_amount" fluid class="ltr" :readonly="readonly" />
                    <label>{{ $t("discount") }}</label>
                </FloatLabel>
            </InputGroup>
        </div>

        <div class="-m-6 mt-0 print:mt-auto rounded-b-md overflow-hidden">
            <div class="border border-[var(--surface-card)] bg-[var(--surface-ground)]">
                <div class="flex justify-between items-center font-bold  px-6 py-4 ">
                    <span>{{ $t("amount-payable") }}</span>
                    <span>{{ [formatNumber(model.deposit_amount || model.final_amount), $t("toman")].join(" ") }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { formatNumber } from '@/utils/format-number';
import { computed } from 'vue';

defineProps({ readonly: { type: Boolean, default: false } })

const model = defineModel()

const date = computed(() => new Date(model.value.start_date).toLocaleString('fa', {
    year: 'numeric', month: 'numeric', day: 'numeric'
}))
</script>

<style lang="scss" scoped></style>