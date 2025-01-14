<template>
    <div class="card border border-transparent" :class="{ 'border-red-500': !!errors }">
        <DataTable :value="rows" class="last:[&_tr]:!bg-[var(--surface-ground)] [&_th]:!bg-[var(--surface-ground)]">
            <Column field="title" :header="$t('installment-terms')" :footer="$t('choice')" footer-class="!text-right" />
            <Column v-for="(plan, i) in installmentPlans.items" :key="i" :field="plan.id"
                :header="`${plan.months_count} ${$t('months')} `" class="!text-center [&_*]:justify-center">
                <template #footer>
                    <div class="flex justify-center">
                        <Button :severity="model.months_count != plan.months_count ? 'secondary' : null"
                            icon="pi pi-check" rounded :disabled="readonly" @click="handleClick(plan)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import { useInstallmentPlansStore } from '@/stores/installment-plans';
import { formatNumber } from '@/utils/format-number';
import { computed, inject } from 'vue';

const props = defineProps({ errors: { type: String } })

const { t } = inject('service')

const model = defineModel()

const installmentPlans = useInstallmentPlansStore()

const rows = computed(() => {
    const list = [
        { title: `${t("advance-payment")} (${t("percent")})`, },
        { title: `${t("advance-payment")} (${t("toman")})` },
        { title: t("installment-every-month") },
        { title: t("final-amount") },
    ];

    installmentPlans.items.forEach((item) => {
        const amount = model.value.final_amount + model.value.final_amount * (item.interest_percent / 100);
        const deposit = Math.floor((item.deposit_percent / 100) * amount / 1000) * 1000;
        list[0][item.id] = `%${item.deposit_percent}`;
        list[1][item.id] = [formatNumber(deposit), t("toman")].join(" ");
        const remaining = amount - deposit;
        list[2][item.id] = [formatNumber(Math.floor(remaining / item.months_count / 1000) * 1000), t("toman")].join(" ");
        list[3][item.id] = [formatNumber(amount), t("toman")].join(" ");

        if (model.value.months_count == item.months_count)
            handleClick(item)
    });

    return list;
});

const handleClick = (plan) => {
    console.log(plan.id);

    const amount = model.value.final_amount + model.value.final_amount * (plan.interest_percent / 100);
    const deposit_amount = Math.floor((plan.deposit_percent / 100) * amount / 1000) * 1000;
    Object.assign(model.value, { months_count: plan.months_count, deposit_amount })
}

</script>

<style lang="scss" scoped></style>