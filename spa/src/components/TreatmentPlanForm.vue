<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex justify-between items-center">
            <label> {{ $t('payment-type') }}</label>
            <SelectButton v-model="form.payment_type" :options="['installments', 'cash']"
                :optionLabel="(item) => $t(item)" class="ltr" />
        </div>
        <Transition name="fade">
            <div v-if="form.payment_type == 'installments'" class="flex flex-wrap gap-2 justify-between items-center">
                <label class="has-[+*+small]:text-red-500"> {{ $t('repayment-period') }}</label>
                <SelectButton v-model="form.months" :options="['3', '6', '9', '12'].reverse()"
                    :optionLabel="(item) => [item, $t('months')].join(' ')"
                    class="ltr [&_span]:rtl has-[+small]:border-red-500 has-[+small]:border" />
                <small v-if="errors.months" v-text="errors.months[0]" class="text-red-500" />
            </div>
        </Transition>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('desc') }}</label>
            <Textarea v-model="form.desc" fluid rows="5" cols="30" class="has-[+small]:!border-red-500" />
            <small v-if="errors.desc" v-text="errors.desc[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useTreatmentPlansStore } from '@/stores/treatment-plans';
import { computed, inject, reactive, ref, watch } from 'vue';

const dialogRef = inject('dialogRef')

const { toast, route } = inject('service')

const { id } = route.params

const form = reactive({
    patient: id,
    payment_type: 'cash',
    desc: ''
})
const errors = ref({})
const loading = ref(false)

const treatmentPlans = useTreatmentPlansStore()

const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await treatmentPlans.store(form)

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
</script>

<style lang="scss" scoped></style>