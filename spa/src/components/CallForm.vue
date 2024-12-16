<template>
    <form @submit.prevent="handleSubmit()"
        class="grid grid-cols-2 gap-x-4 gap-y-8 [&_small]:-mb-6 w-full md:w-[40rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="patient" :options="patients.items" option-value="id" :loading="patients.fetching"
                    resetFilterOnHide autoFilterFocus filter
                    :filterFields="[({ mobiles }) => mobiles.map(m => m.number).join(' ')]"
                    :optionLabel="({ firstname, lastname }) => [firstname, lastname].join(' ')" fluid
                    :invalid="errors['patient.id']" :disabled="disabled"
                    panel-class="[&_.p-iconfield]:ltr [&_input:not(.p-filled)]:!text-right"
                    :filter-placeholder="$t('search-patient')" @filter="patients.search($event.value)">
                    <template #empty>
                        <i></i>
                    </template>
                    <template #emptyfilter>
                        <span v-if="!patients.fetching">{{ $t('not-found') }}</span>
                        <span v-else>{{ $t('searching') }}</span>
                    </template>
                </Select>
                <label> {{ $t('patient') }}</label>
            </FloatLabel>
            <small v-if="errors['patient.id']" v-text="errors['patient.id'][0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.mobile" :options="patients.item?.mobiles" optionLabel="number"
                    optionValue="number" fluid :loading="patients.fetching" :invalid="errors.mobile" panel-class="ltr"
                    class="text-left *:!pl-0" />
                <label>{{ $t('mobile') }}</label>
            </FloatLabel>
            <small v-if="errors.mobile" v-text="errors.mobile[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.patient.status" :options="patientStatuses" optionValue="id" fluid
                    :invalid="errors['patient.status']"
                    :disabled="patients.statuses.filter(s => ['in-person-visit', 'online-visit'].includes(s.name)).map(s => s.id).includes(form.patient.status)">
                    <template #value="{ value }">
                        <Tag v-if="value" class="text-xs" v-bind="patients.statuses.find(({ id }) => value == id)" />
                    </template>
                    <template #option="{ option }">
                        <Tag v-bind="option" class="text-xs" />
                    </template>
                </Select>
                <label> {{ $t('patient-status') }}</label>
            </FloatLabel>
            <small v-if="errors['patient.status']" v-text="errors['patient.status'][0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.status" :options="store.statuses" optionValue="id" fluid :invalid="errors.status">
                    <template #value="{ value }">
                        <Tag v-if="value" class="text-xs" v-bind="store.statuses.find(({ id }) => value == id)" />
                    </template>
                    <template #option="{ option }">
                        <Tag v-bind="option" class="text-xs" />
                    </template>
                </Select>
                <label> {{ $t('call_status') }}</label>
            </FloatLabel>
            <small v-if="errors.status" v-text="errors.status[0]" class="text-red-500" />
        </div>

        <div class="flex flex-col gap-1 col-span-2">
            <FloatLabel variant="on">
                <Textarea v-model="form.desc" fluid rows="5" cols="30" :invalid="errors.desc" />
                <label> {{ $t('desc') }}</label>
            </FloatLabel>
            <small v-if="errors.desc" v-text="errors.desc[0]" class="text-red-500" />
        </div>

        <template v-if="form.follow_up">
            <div class="flex flex-col gap-1">
                <FloatLabel variant="on">
                    <DatePicker v-model="form.follow_up.due_date" :invalid="errors['follow_up.due_date']" class="ltr"
                        fluid dateFormat="yy/mm/dd" show-time :min-date="new MyDate()" :disabledDates="holidays.items"
                        :holidayDates="holidays.items" />
                    <label>{{ $t('follow-up-date') }}</label>
                </FloatLabel>
                <small v-if="errors['follow_up.due_date']" v-text="errors['follow_up.due_date'][0]"
                    class="text-red-500" />
            </div>
            <div class="flex flex-col gap-1 col-span-2">
                <FloatLabel variant="on">
                    <Textarea v-model="form.follow_up.desc" fluid rows="5" cols="30"
                        :invalid="errors['follow_up.desc']" />
                    <label> {{ $t('follow-up-desc') }}</label>
                </FloatLabel>
                <small v-if="errors['follow_up.desc']" v-text="errors['follow_up.desc'][0]" class="text-red-500" />
            </div>
        </template>
        <div class="flex justify-between col-span-2 gap-2 mt-4">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import MyDate from '../utils/MyDate'
import { useHolidaysStore } from '@/stores/holidays';
import { useCallsStore } from '@/stores/calls';
import { useFollowUpsStore } from '@/stores/follow-ups';
import { usePatientsStore } from '@/stores/patients';
import { computed, onMounted, watch } from 'vue';
import { inject, reactive, ref } from 'vue';

const { route, toast } = inject('service')

const { id } = route.params

const dialogRef = inject('dialogRef')
const { data: followUpId } = dialogRef.value

const form = reactive({ patient: {} })
const errors = ref({})
const loading = ref(false)
const disabled = ref(false)

const store = useCallsStore()
if (store.items.length === 0)
    store.index()

const followUps = useFollowUpsStore()

const holidays = useHolidaysStore()
holidays.index()

const patients = usePatientsStore()
patients.index()
const patientStatuses = computed(() => patients.statuses.filter(s => ['no-status', 'in-progress', 'not-needed'].includes(s.name)))


const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await store.store(form)

    loading.value = false

    if (statusText === 'OK') {
        if (followUpId) {
            const followUp = followUps.items.find(f => f.id == followUpId)
            followUp.status = followUps.statuses.find(s => s.name == 'done')
            const p = patients.items.find(p => p.id == form.patient.id)
            p.status = patients.statuses.find(s => s.id == form.patient.status)
        }
        if (id) patients.show(id)
        dialogRef.value.close();
    }
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}

const patient = ref()

watch(patient, async (v) => {
    if (v) {
        await patients.show(v)

        const item = patients.item

        if (item) {
            form.patient.id = item.id
            form.patient.status = item.status.id

            if (item.mobiles.length === 1)
                form.mobile = item.mobiles[0].number
            else {
                const mobile = item.mobiles.find(m => m.number.includes(patients.filters.query))

                if (mobile) form.mobile = mobile.number
            }
            delete errors.value[`patient.id`]
            delete errors.value[`patient.status`]

        } else patient.value = undefined
    }
})

watch(() => form.patient.status, (v) => {
    const status = patients.statuses.find(s => s.name === 'in-progress')
    if (status?.id === v) form.follow_up = {}
    else delete form.follow_up
})

watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (key == 'follow_up') {
            ['due_date', 'desc'].forEach(k => {
                if (value.follow_up[k] != old.follow_up?.[k]) delete errors.value[`follow_up.${k}`]
            })
        }
        if (value[key] != old[key]) delete errors.value[key]
    })
}, { deep: true })

onMounted(() => {
    if (id) {
        patient.value = +id
        disabled.value = true
    }
    else if (store.filters.patient)
        patient.value = +store.filters.patient

    if (followUpId) {
        const followUp = followUps.items.find(f => f.id == followUpId)
        if (followUp) {
            patient.value = followUp.patient.id
            form.follow_up_id = followUpId
            disabled.value = true
        }
    }
})
</script>

<style lang="scss" scoped></style>