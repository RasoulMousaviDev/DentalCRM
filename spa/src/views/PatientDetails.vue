<template>
    <div class="flex flex-col">
        <PatientInfo :data="patient" />
        <Tabs :value="0" class="card">
            <TabList>
                <Tab v-for="(tab, i) in tabs" :key="i" :value="i">
                    {{ $t(tab) }}
                </Tab>
            </TabList>
            <TabPanels class="!px-0">
                <TabPanel v-for="(tab, i) in tabs" :key="i" :value="i">
                    <PatientCalls v-if="tab == 'calls'" />
                    <PatientFollowUps v-else-if="tab == 'follow-ups'" />
                    <PatientAppointments v-else-if="tab == 'appointments'" />
                    <PatientDocuments v-else-if="tab == 'documents'" />
                    <PatientTreatmentPlans v-else-if="tab == 'treatment-plans'" />
                </TabPanel>
            </TabPanels>
        </Tabs>
    </div>
</template>

<script setup>
import PatientAppointments from '@/components/PatientAppointments.vue';
import PatientCalls from '@/components/PatientCalls.vue';
import PatientDocuments from '@/components/PatientDocuments.vue';
import PatientFollowUps from '@/components/PatientFollowUps.vue';
import PatientInfo from '@/components/PatientInfo.vue';
import PatientTreatmentPlans from '@/components/PatientTreatmentPlans.vue';
import { usePatientsStore } from '@/stores/patients';
import { computed, inject, reactive, ref } from 'vue';

const { dialog, confirm, toast, route, router, t } = inject('service')

const { id } = route.params

const store = usePatientsStore()

const patient = computed(() => store.items.find((item) => item.id == id))

if (!patient.value)
    router.replace({ name: 'Patients' });

const tabs = reactive(['calls', 'follow-ups', 'appointments', 'treatment-plans', 'documents'])
</script>

<style lang="scss" scoped></style>