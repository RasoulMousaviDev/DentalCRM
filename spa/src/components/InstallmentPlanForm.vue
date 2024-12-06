<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-8 [&_small]:-mb-6 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputNumber v-model="form.months_count" class="ltr" fluid :invalid="errors.months" />
                <label>{{ $t('months-count') }}</label>
            </FloatLabel>
            <small v-if="errors.months" v-text="errors.months[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputNumber v-model="form.deposit_percent" class="ltr" fluid :invalid="errors.deposit_percent" :min="0"
                    :max="100" />
                <label>{{ $t('deposit-percent') }}</label>
            </FloatLabel>
            <small v-if="errors.deposit_percent" v-text="errors.deposit_percent[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputNumber v-model="form.interest_percent" class="ltr" fluid :invalid="errors.interest_percent" :min="0"
                    :max="100" />
                <label>{{ $t('interest-percent') }}</label>
            </FloatLabel>
            <small v-if="errors.interest_percent" v-text="errors.interest_percent[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between items-center pr-2">
            <label> {{ $t('status') }}</label>
            <ToggleButton v-model="form.status" class="w-20" :onLabel="$t('active')" :offLabel="$t('deactive')" />
        </div>
        <div class="flex justify-between gap-2 mt-4">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useInstallmentPlansStore } from '@/stores/installment-plans';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast } = inject('service')

const store = useInstallmentPlansStore()

const dialogRef = inject('dialogRef')
const { plan } = dialogRef.value.data || {}

const form = reactive({ months_count: null, deposit_percent: null, interest_percent: null, status: true })
const errors = ref({})
const loading = ref(false)


const handleSubmit = async () => {
    loading.value = true

    let result;
    if (plan)
        result = await store.update(plan.id, form)
    else
        result = await store.store(form)

    const { status, statusText, data } = result

    loading.value = false

    if (statusText === 'OK')
        dialogRef.value.close();
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}


watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})

onMounted(() => {
    if (plan)
        Object.keys(form).forEach((key) => form[key] = plan[key])
})
</script>

<style lang="scss" scoped></style>