<template>
    <div class="flex flex-col gap-8">
        <PatientInfo :data="store.item" />
        <Tabs :value="0" class="card" lazy>
            <TabList>
                <Tab v-for="(tab, i) in tabs" :key="i" :value="i">
                    {{ $t(tab) }}
                </Tab>
            </TabList>
            <TabPanels class="!px-0">
                <TabPanel v-for="(tab, i) in tabs" :key="i" :value="i"
                    class="*:p-0 [&_.p-datatable-header>div>div>span]:hidden">
                    <Calls v-if="tab == 'calls'" />
                    <FollowUps v-else-if="tab == 'follow-ups'" />
                    <Appointments v-else-if="tab == 'appointments'" />
                    <PatientPhotos v-else-if="tab == 'documents'" />
                    <TreatmentPlans v-else-if="tab == 'treatment-plans'" />
                </TabPanel>
            </TabPanels>
        </Tabs>
    </div>
</template>

<script setup>
import PatientInfo from '@/components/PatientInfo.vue';
import { usePatientsStore } from '@/stores/patients';
import { inject, reactive, ref } from 'vue';
import Calls from './Calls.vue';
import FollowUps from './FollowUps.vue';
import Appointments from './Appointments.vue';
import TreatmentPlans from './TreatmentPlans.vue';
import PatientPhotos from '@/components/PatientPhotos.vue';

const tabs = reactive(['calls', 'follow-ups', 'appointments', 'treatment-plans', 'documents'])

const { route } = inject('service')

const { id } = route.params

const store = usePatientsStore()

store.show(id)

</script>

<style lang="scss" scoped></style>