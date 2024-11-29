<template>
    <div class="flex flex-col gap-8">
        <div class="card flex items-center justify-between !mb-0">
            <span class="text-2xl font-bold ml-auto">
                {{ $t('dashboard') }}
            </span>
            <div class="flex gap-3">
                <InputGroup class="ltr !w-[19rem]">
                    <DatePicker v-model="date.from" inputClass="ltr" panelClass="ltr" dateFormat="yy/mm/dd"
                        :max-date="new Date()" />
                    <InputGroupAddon>{{ $t('from') }}</InputGroupAddon>
                </InputGroup>
                <InputGroup class="ltr !w-[19rem]">
                    <DatePicker v-model="date.to" inputClass="ltr" panelClass="ltr" dateFormat="yy/mm/dd"
                        />
                    <InputGroupAddon>{{ $t('to') }}</InputGroupAddon>
                </InputGroup>
                <Button :label="$t('search')" :loading="store.fetching" @click="store.getCharts(form)" />
            </div>
        </div>
        <ul class="w-full flex gap-8">
            <li v-for="(card, i) in cards" :key="i" class="flex-1 grow">
                <div class="card mb-0 flex flex-col gap-4 h-full">
                    <div class="flex justify-between">
                        <div class="flex flex-col gap-4">
                            <span class="text-lg text-muted-color font-medium">
                                {{ $t(card.title) }}
                            </span>
                            <div class="text-surface-600 font-medium text-xl">
                                {{ card.count }}
                            </div>
                        </div>
                        <div class="w-14 h-14 flex rounded-border" :class="card.class">
                            <i :class="card.icon" class="pi !text-3xl m-auto"></i>
                        </div>
                    </div>
                    <template v-if="card.details">
                        <span class="text-surface-600 font-medium">{{ }}</span>
                        <span class="text-muted-color">{{ }}</span>
                    </template>
                </div>
            </li>
        </ul>
        <div class="flex flex-wrap gap-8">
            <PatientGenderChart :data="store.charts.patientGenders" />
            <PatientLeadSourceChart :data="store.charts.patientLeadSources" />
            <PatientTreatmentChart :data="store.charts.patientTreatments" />
            <PatientStatusChart :data="store.charts.patientStatuses" />
            <CallStatusChart :data="store.charts.callStatuses" />
            <ReceptionChart :data="store.charts.receptionReport" />
        </div>
    </div>
</template>


<script setup>
import CallStatusChart from '@/components/CallStatusChart.vue';
import PatientGenderChart from '@/components/PatientGenderChart.vue';
import PatientLeadSourceChart from '@/components/PatientLeadSourceChart.vue';
import PatientStatusChart from '@/components/PatientStatusChart.vue';
import PatientTreatmentChart from '@/components/PatientTreatmentChart.vue';
import ReceptionChart from '@/components/AppointmentStatusChart.vue';
import { useDashboardStore } from '@/stores/dashboard';

import { reactive, computed } from "vue";

const store = useDashboardStore()

const fdate = new Date();
fdate.setMonth(fdate.getMonth() - 1)

const date = reactive({ from: fdate, to: new Date() })
const dateFormat = (d) => `${d.getFullYear()}-${('0' + (d.getMonth() + 1)).slice(-2)}-${('0' + d.getDate()).slice(-2)}`
const form = reactive({
    from: computed(() => dateFormat(date.from) + ' 00:00:00'),
    to: computed(() => dateFormat(date.to) + ' 23:59:59'),
})

store.getCharts(form)

const cards = reactive([
    {
        title: 'patients',
        class: 'text-green-500 bg-green-200 dark:bg-green-400/10',
        icon: 'pi-users',
        count: computed(() => store.charts.patientCount)
    },
    {
        title: 'calls',
        class: 'text-blue-500 bg-blue-200 dark:bg-blue-400/10',
        icon: 'pi-phone',
        count: computed(() => store.charts.callCount)
    },
    {
        title: 'appointments',
        class: 'text-purple-500 bg-purple-200 dark:bg-purple-400/10',
        icon: 'pi-calendar-times',
        count: computed(() => store.charts.appointmentCount)
    },
    {
        title: 'follow-ups',
        class: 'text-orange-500 bg-orange-200 dark:bg-orange-400/10',
        icon: 'pi-pen-to-square',
        count: computed(() => store.charts.followUpCount)
    },
])
</script>



<style lang="scss" scoped></style>