<template>
    <form @submit.prevent="handleSubmit()"
        class="grid grid-cols-2 gap-x-4 gap-y-8 [&_small]:-mb-6 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1 col-span-2">
            <FloatLabel variant="on">
                <InputText v-model="form.title" fluid :invalid="errors.title" />
                <label>{{ $t('title') }}</label>
            </FloatLabel>
            <small v-if="errors.title" v-text="errors.title[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1 col-span-2">
            <FloatLabel variant="on">
                <Textarea v-model="form.desc" fluid rows="5" cols="30" :invalid="errors.desc" />
                <label> {{ $t('desc') }}</label>
            </FloatLabel>
            <small v-if="errors.desc" v-text="errors.desc[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <DatePicker v-model="form.start_date" :invalid="errors.start_date" class="ltr" fluid
                    dateFormat="yy/mm/dd" :min-date="new Date()" />
                <label>{{ $t('start-date') }}</label>
            </FloatLabel>
            <small v-if="errors.start_date" v-text="errors.start_date[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <DatePicker v-model="form.end_date" :invalid="errors.end_date" class="ltr" fluid dateFormat="yy/mm/dd"
                    :min-date="new Date()" />
                <label>{{ $t('start-date') }}</label>
            </FloatLabel>
            <small v-if="errors.end_date" v-text="errors.end_date[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1 col-span-2">
            <InputGroup class="ltr">
                <InputGroupAddon class="!px-4">{{ $t('toman') }}</InputGroupAddon>
                <FloatLabel variant="on" class="rtl">
                    <InputNumber v-model="form.budget" fluid class="ltr" :invalid="errors.budget"
                        @input="delete errors.budget" />
                    <label>{{ $t('budget') }}</label>
                </FloatLabel>
            </InputGroup>
            <small v-if="errors.budget" v-text="errors.budget[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between gap-2 col-span-2 mt-4">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useCampainsStore } from '@/stores/campains';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast } = inject('service')

const dialogRef = inject('dialogRef')
const { campain } = dialogRef.value.data || {}

const form = reactive({ title: '', desc: '', start_date: '', end_date: '', budget: null })
const errors = ref({})
const loading = ref(false)

const store = useCampainsStore()

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (campain)
        result = await store.update(campain.id, form)
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
    if (campain)
        Object.keys(form).forEach((key) => form[key] = campain[key])
})
</script>

<style lang="scss" scoped></style>