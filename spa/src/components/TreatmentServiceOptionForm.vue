<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-8 [&_small]:-mb-6 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputText v-model="form.title" fluid :invalid="errors.title" />
                <label>{{ $t('title') }}</label>
            </FloatLabel>
            <small v-if="errors.title" v-text="errors.title[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <InputGroup class="ltr">
                <InputGroupAddon class="!px-4">{{ $t('toman') }}</InputGroupAddon>
                <FloatLabel variant="on" class="rtl">
                    <InputNumber v-model="form.cost" fluid :invalid="errors.cost" class="ltr" @input="delete errors.cost" />
                    <label>{{ $t('cost') }}</label>
                </FloatLabel>
            </InputGroup>
            <small v-if="errors.cost" v-text="errors.cost[0]" class="text-red-500" />
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
import { useTreatmentServiceOptionsStore } from '@/stores/treatment-service-options';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast } = inject('service')

const dialogRef = inject('dialogRef')
const { option } = dialogRef.value.data || {}

const form = reactive({ title: '', cost: null, status: true })
const errors = ref({})
const loading = ref(false)

const options = useTreatmentServiceOptionsStore()

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (option)
        result = await options.update(option.id, form)
    else
        result = await options.store(form)

    const { status, statusText, data } = result

    loading.value = false

    if (statusText === 'OK')
        dialogRef.value.close();
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}


watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})

onMounted(() => {
    if (option)
        Object.keys(form).forEach((key) => form[key] = option[key])
})
</script>

<style lang="scss" scoped></style>