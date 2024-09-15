<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2 grow">
            <label class="has-[+*+small]:text-red-500">{{ $t('treatment') }}</label>
            <Select v-model="form.treatment" :options="treatments.items" :loading="treatments.fetching"
                optionLabel="title" optionValue="id" fluid checkmark :placeholder="$t('choose')"
                class="has-[+small]:!border-red-500" />
            <small v-if="errors.treatment" v-text="errors.treatment[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500">{{ $t('appointment-date') }}</label>
            <DatePicker v-model="form.due_date" :placeholder="$t('choose')"
                class="w-full [&>input]:has-[+small]:!border-red-500" :inputClass="{ 'ltr': form.due_date }"
                panelClass="ltr" dateFormat="yy/mm/dd" showTime hourFormat="24" />
            <small v-if="errors.due_date" v-text="errors.due_date[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 col-span-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('desc') }}</label>
            <Textarea v-model="form.desc" fluid rows="5" cols="30" />
        </div>
        <div class="flex justify-between col-span-2 gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useAppointmentsStore } from '@/stores/appointments';
import { useTreatmentsStore } from '@/stores/treatments';
import { computed, inject, reactive, ref, watch } from 'vue';

const dialogRef = inject('dialogRef')

const { route } = inject('service')

const { id } = route.params

const form = reactive({ patient: id })
const errors = ref({})
const loading = ref(false)

const treatments = useTreatmentsStore()
treatments.index()

const appointment = useAppointmentsStore()

const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await appointment.store(form)

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