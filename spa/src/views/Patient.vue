<template>
    <div class="flex flex-col">
        <PatientInfo :data="store.item" />
        <Tabs :value="0" class="card">
            <TabList>
                <Tab v-for="(tab, i) in tabs" :key="i" :value="i">
                    {{ $t(tab) }}
                </Tab>
            </TabList>
            <TabPanels class="!px-0">
                <TabPanel v-for="(tab, i) in tabs" :key="i" :value="i">
                    <PatientCalls v-if="tab == 'calls'" />
                    <PatientFollowups v-else-if="tab == 'follow-ups'" />
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
import PatientDocuments from '@/components/PatientPhotos.vue';
import PatientFollowups from '@/components/PatientFollowups.vue';
import PatientInfo from '@/components/PatientInfo.vue';
import PatientTreatmentPlans from '@/components/PatientTreatmentPlans.vue';
import { usePatientsStore } from '@/stores/patients';
import { inject, reactive, ref } from 'vue';

const {route } = inject('service')

const { id } = route.params

const store = usePatientsStore()

store.show(id)

const tabs = reactive(['calls', 'follow-ups', 'appointments', 'treatment-plans', 'documents'])
</script>

<style lang="scss" scoped></style>