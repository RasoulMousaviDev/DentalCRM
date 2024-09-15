<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500">{{ $t('amount') }}</label>
            <InputGroup class="ltr [&_*]:has-[+small]:!border-red-500 [&>div]:has-[+small]:text-red-500">
                <InputGroupAddon>{{ $t('toman') }}</InputGroupAddon>
                <InputNumber v-model="form.amount" class="w-full" @input="delete errors.amount" />
            </InputGroup>
            <small v-if="errors.amount" v-text="errors.amount[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500">{{ $t('payment-date') }}</label>
            <DatePicker v-model="form.payment_date" showTime class="w-full [&>input]:has-[+small]:!border-red-500 [&>input]:has-[+small]:placeholder:!text-red-500"
                :placeholder="$t('choose')" :inputClass="{ 'ltr': form.payment_date }" panelClass="ltr"
                dateFormat="yy/mm/dd" />
            <small v-if="errors.payment_date" v-text="errors.payment_date[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between col-span-2 gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useDepositsStore } from '@/stores/deposits';
import { computed, inject, reactive, ref, watch } from 'vue';

const { router } = inject('service')

const dialogRef = inject('dialogRef')

const form = reactive({
    appointment: dialogRef.value.data.appointment.id,
    amount: null,
    payment_date: null
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
        store.index({})
        router.push({ name: 'Deposits' })
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