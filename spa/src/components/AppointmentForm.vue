<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-8 [&_small]:-mb-6 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.patient" :options="patients.items" option-value="id" :loading="patients.fetching"
                    resetFilterOnHide autoFilterFocus filter
                    :filterFields="[({ mobiles }) => mobiles.map(m => m.number).join(' ')]"
                    :optionLabel="({ firstname, lastname }) => [firstname, lastname].join(' ')" fluid
                    :invalid="errors.patient" :disabled="$route.name == 'Patient'"
                    panel-class="[&_.p-iconfield]:ltr [&_input:not(.p-filled)]:!text-right"
                    :filter-placeholder="$t('search-patient')" @filter="patients.search($event.value)">
                </Select>
                <label> {{ $t('patient') }}</label>
            </FloatLabel>
            <small v-if="errors.patient" v-text="errors.patient[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <MultiSelect v-model="form.treatments" display="chip" :options="treatments.items"
                    :loading="treatments.fetching" optionLabel="title" optionValue="id" :showToggleAll="false" fluid
                    :invalid="errors.treatments" />
                <label>{{ $t('treatments') }}</label>
            </FloatLabel>
            <small v-if="errors.treatments" v-text="errors.treatments[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <DatePicker v-model="form.due_date" :invalid="errors.due_date" class="ltr" fluid dateFormat="yy/mm/dd"
                    show-time :min-date="new MyDate()" :disabledDates="holidays.items"/>
                <label>{{ $t('appointment-date') }}</label>
            </FloatLabel>
            <small v-if="errors.due_date" v-text="errors.due_date[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Textarea v-model="form.desc" fluid rows="5" cols="30" />
                <label> {{ $t('desc') }}</label>
            </FloatLabel>
        </div>
        <div class="flex justify-between gap-2 mt-4">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import MyDate from '../utils/MyDate'
import { useAppointmentsStore } from '@/stores/appointments';
import { usePatientsStore } from '@/stores/patients';
import { useTreatmentsStore } from '@/stores/treatments';
import { useHolidaysStore } from '@/stores/holidays';
import { computed, inject, reactive, ref, watch } from 'vue';

const dialogRef = inject('dialogRef')

const { route } = inject('service')

const { id } = route.params

const form = reactive({ patient: +id || null })
const errors = ref({})
const loading = ref(false)

const patients = usePatientsStore()
const treatments = useTreatmentsStore()
treatments.index()

const holidays = useHolidaysStore()
holidays.index()

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