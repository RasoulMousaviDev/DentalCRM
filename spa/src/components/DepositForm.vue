<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-8 [&_small]:-mb-6 w-full md:w-[24rem] pt-2">
        <div class="flex flex-col gap-1">
            <InputGroup class="ltr">
                <InputGroupAddon class="!px-4">{{ $t('toman') }}</InputGroupAddon>
                <FloatLabel variant="on" class="rtl">
                    <InputNumber v-model="form.amount" fluid :invalid="errors.amount" class="ltr"
                        @input="delete errors.amount" />
                    <label>{{ $t('amount') }}</label>
                </FloatLabel>
            </InputGroup>
            <small v-if="errors.amount" v-text="errors.amount[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <DatePicker v-model="form.payment_date" :invalid="errors.payment_date" class="ltr" fluid
                    dateFormat="yy/mm/dd" show-time :min-date="new Date()" />
                <label>{{ $t('payment-date') }}</label>
            </FloatLabel>
            <small v-if="errors.payment_date" v-text="errors.payment_date[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between col-span-2 gap-2 mt-4">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useDepositsStore } from '@/stores/deposits';
import { computed, inject, reactive, ref, watch } from 'vue';

const { toast } = inject('service')

const dialogRef = inject('dialogRef')

const { data } = dialogRef.value

const form = reactive({
    appointment: data.appointment.id,
    amount: null,
    payment_date: new Date()
})
const errors = ref({})
const loading = ref(false)

const deposits = useDepositsStore()

const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await deposits.store(form)

    loading.value = false

    if (statusText === 'OK') {
        dialogRef.value.close();
    }
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
</script>

<style lang="scss" scoped></style>