<template>
    <form @submit.prevent="handleSubmit()" class="grid grid-cols-2 gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('call_status') }}</label>
            <Select v-model="form.status" :options="callStatuses.items" :loading="callStatuses.fetching"
                optionValue="id" fluid checkmark :placeholder="$t('choose')" class="has-[+small]:!border-red-500">
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="callStatuses.items.find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
            <small v-if="errors.status" v-text="errors.status[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 grow">
            <label class="has-[+*+small]:text-red-500">{{ $t('mobile') }}</label>
            <Select v-model="form.mobile" :options="patient.mobiles" optionLabel="number" optionValue="number" fluid
                checkmark class="ltr has-[+small]:!border-red-500" panel-class="ltr" />
            <small v-if="errors.mobile" v-text="errors.mobile[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 col-span-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('desc') }}</label>
            <Textarea v-model="form.desc" fluid rows="5" cols="30" class="has-[+small]:!border-red-500" />
            <small v-if="errors.desc" v-text="errors.desc[0]" class="text-red-500" />
        </div>
        <hr class="col-span-2">
        <div class="flex flex-col gap-2" :class="{ 'col-span-2': !followup }">
            <label class="has-[+*+small]:text-red-500"> {{ $t('patient_status') }}</label>
            <Select v-model="form.patient_status" :options="patientStatuses.items" :loading="patientStatuses.fetching"
                optionValue="id" fluid checkmark :placeholder="$t('choose')" class="has-[+small]:!border-red-500">
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="patientStatuses.items.find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
            <small v-if="errors.patient_status" v-text="errors.patient_status[0]" class="text-red-500" />
        </div>
        <template v-if="form.followup">
            <div class="flex flex-col gap-2">
                <label class="has-[+*+small]:text-red-500">{{ $t('followup-date') }}</label>
                <DatePicker v-model="form.followup.due_date" class="w-full [&>input]:has-[+small]:!border-red-500"
                    :placeholder="$t('choose')" :inputClass="{ 'ltr': form.due_date }"
                    panelClass="ltr -translate-x-10" dateFormat="yy/mm/dd" showTime hourFormat="24" />
                <small v-if="errors['followup.due_date']" v-text="errors['followup.due_date'][0]"
                    class="text-red-500" />
            </div>
            <div class="flex flex-col gap-2 col-span-2">
                <label class="has-[+*+small]:text-red-500"> {{ $t('followup-desc') }}</label>
                <Textarea v-model="form.followup.desc" fluid rows="5" cols="30" class="has-[+small]:!border-red-500" />
                <small v-if="errors['followup.desc']" v-text="errors['followup.desc'][0]" class="text-red-500" />
            </div>
        </template>
        <div class="flex justify-between col-span-2 gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useCallStatuesStore } from '@/stores/call-statuses';
import { useCallsStore } from '@/stores/calls';
import { usePatientStatuesStore } from '@/stores/patient-statuses';
import { usePatientsStore } from '@/stores/patients';
import { computed, watch } from 'vue';
import { inject, reactive, ref } from 'vue';

const { route } = inject('service')

const { id } = route.params

const dialogRef = inject('dialogRef')

const form = reactive({ })
const errors = ref({})
const loading = ref(false)

const calls = useCallsStore()

const callStatuses = useCallStatuesStore()
callStatuses.index()

const patientStatuses = usePatientStatuesStore()
patientStatuses.index()

const patientStore = usePatientsStore()
const patient = reactive(patientStore.items.find((item) => item.id == id))
const followup = computed(() => [1].includes(form.patient_status))

const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await calls.store(form)

    loading.value = false

    if (statusText === 'OK')
        dialogRef.value.close();
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}

watch(followup, (v) => {
    if (v) form.followup = {}
    else delete form.followup
})

watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})

form.patient = id
form.patient_status = patient.status.id

if (dialogRef.value.data)
    form.followup_id = dialogRef.value.data

if (patient.mobiles.length === 1)
    form.mobile = patient.mobiles[0].number
</script>

<style lang="scss" scoped></style>