<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-wrap w-full gap-4 [&>span]:w-52">
        <FloatLabel variant="on">
            <InputText v-model="filters.id" fluid class="ltr" v-keyfilter.int />
            <label>{{ $t('id') }}</label>
        </FloatLabel>
        <FloatLabel v-if="['super-admin', 'admin'].includes(auth.user?.role.name)" variant="on">
            <InputText v-model="filters.user" fluid />
            <label>{{ $t('consultant') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.firstname" fluid />
            <label>{{ $t('firstname') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.lastname" fluid />
            <label>{{ $t('lastname') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.mobile" fluid class="ltr" v-keyfilter.int />
            <label>{{ $t('mobile') }} </label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.telephone" fluid class="ltr" v-keyfilter.int />
            <label> {{ $t('telephone') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.birthday" selectionMode="range" :manualInput="false" :max-date="new Date()"
                fluid class="ltr" dateFormat="yy/mm/dd" showButtonBar />
            <label>{{ $t('birthday') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.gender" :options="genders.items" :optionLabel="(opt) => $t(opt)" fluid
                show-clear />
            <label>{{ $t('gender') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.treatment" :options="treatments.items" :loading="treatments.fetching"
                optionLabel="title" optionValue="id" fluid show-clear />
            <label> {{ $t('treatment') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.province" :options="provinces.items" :loading="provinces.fetching"
                optionLabel="title" optionValue="id" fluid show-clear />
            <label> {{ $t('province') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.city" :options="filters.province ? cities.items : []" :loading="cities.fetching"
                :emptyMessage="$t('first-select-province')" optionLabel="title" optionValue="id" fluid show-clear />
            <label> {{ $t('city') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.lead_source" :options="leadSources.items" :loading="leadSources.fetching"
                optionLabel="title" optionValue="id" fluid show-clear />
            <label> {{ $t('lead-source') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.insurance" :options="['has', 'not-has']" :optionLabel="(opt) => $t(opt)"
                :optionValue="(opt) => opt == 'has'" fluid show-clear />
            <label> {{ $t('insurance') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.status" :options="store.statuses" optionValue="id" fluid show-clear>
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="store.statuses.find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
            <label> {{ $t('patient-status') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.created_at" selectionMode="range" :manualInput="false" class="ltr w-full"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new Date()" />
            <label> {{ $t('created_at') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.updated_at" selectionMode="range" :manualInput="false" class="ltr w-full"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new Date()" />
            <label> {{ $t('updated_at') }}</label>
        </FloatLabel>
        <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" class="w-28 shrink-0 mr-auto"
            :loading="store.fetching" />
    </form>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
import { useCitiesStore } from '@/stores/cities';
import { useGendersStore } from '@/stores/genders';
import { useLeadSourcesStore } from '@/stores/lead-sources';
import { usePatientsStore } from '@/stores/patients';
import { useProvincesStore } from '@/stores/provinces';
import { useTreatmentsStore } from '@/stores/treatments';
import { onBeforeUnmount, onMounted, reactive, watch } from 'vue';

const filters = reactive({})

const store = usePatientsStore()
const provinces = useProvincesStore()
const leadSources = useLeadSourcesStore()
const cities = useCitiesStore()
const genders = useGendersStore()
const treatments = useTreatmentsStore()
const auth = useAuthStore()

treatments.index()
provinces.index()
leadSources.index()

const handleSubmit = async () => {
    store.filters = filters
    store.index()
}

watch(filters, () => {
    Object.entries(filters).forEach(([key, value]) => {
        if (value === null || value.length === 0) delete filters[key]
    })
}, { deep: true })

watch(() => filters.province, (v) => cities.index(v))

onMounted(() => Object.assign(filters, store.filters))

onBeforeUnmount(() => store.filters = {})
</script>

<style lang="scss" scoped></style>