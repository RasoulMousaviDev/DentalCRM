<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-wrap w-full gap-4 [&>span]:w-52">
        <template v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)">
            <FloatLabel variant="on">
                <Select v-model="filters.phone_consultant"
                    :options="users.items.filter((user) => user.roles.some((role) => role.name == 'phone-consultant') && user.status)"
                    option-label="name" option-value="name" fluid show-clear />
                <label>{{ $t('phone-consultant') }}</label>
            </FloatLabel>
            <FloatLabel variant="on">
                <Select v-model="filters.on_site_consultant"
                    :options="users.items.filter((user) => user.roles.some((role) => role.name == 'on-site-consultant') && user.status)"
                    option-label="name" option-value="name" fluid show-clear />
                <label>{{ $t('on-site-consultant') }}</label>
            </FloatLabel>
        </template>
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
            <DatePicker v-model="filters.birthday" selectionMode="range" :manualInput="false" :max-date="new MyDate()"
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
            <MultiSelect v-model="filters.status" :options="store.statuses" optionValue="id" fluid show-clear
                :show-toggle-all="false">
                <template #value="{ value }">
                    <Tag v-for="s in value" class="text-xs" v-bind="store.statuses.find(({ id }) => s == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </MultiSelect>
            <label> {{ $t('patient-status') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.call_status" :options="callStore.statuses" optionValue="id"
                :loading="callStore.fetching" fluid show-clear>
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="callStore.statuses.find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
            <label> {{ $t('call-status') }}</label>
        </FloatLabel>
        <label class="flex items-center gap-3 w-52 p-inputtext cursor-pointer">
            <Checkbox v-model="filters.no_call" binary />
            <span> {{ $t('no-call') }} </span>
        </label>
        <label class="flex items-center gap-3 w-52 p-inputtext cursor-pointer">
            <Checkbox v-model="filters.no_pending_follow_up" binary />
            <span> {{ $t('no-pending-follow-up') }} </span>
        </label>
        <template v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)">
            <label class="flex items-center gap-3 w-52 p-inputtext cursor-pointer">
                <Checkbox v-model="filters.no_phone_consultant" binary />
                <span> {{ $t('no-phone-consultant') }} </span>
            </label>
        </template>
        <label class="flex items-center gap-3 w-52 p-inputtext cursor-pointer">
            <Checkbox v-model="filters.no_treatment_plan" binary />
            <span> {{ $t('no-treatment-plan') }} </span>
        </label>
        <label class="flex items-center gap-3 w-52 p-inputtext cursor-pointer">
            <Checkbox v-model="filters.has_late_follow_up" binary />
            <span> {{ $t('has-late-follow-up') }} </span>
        </label>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.created_at" selectionMode="range" :manualInput="false" class="ltr w-full"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new MyDate()" />
            <label> {{ $t('created_at') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.updated_at" selectionMode="range" :manualInput="false" class="ltr w-full"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new MyDate()" />
            <label> {{ $t('updated_at') }}</label>
        </FloatLabel>
        <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" class="w-28 shrink-0 mr-auto"
            :loading="store.fetching" />
    </form>
</template>

<script setup>
import MyDate from '../utils/MyDate'
import { useAuthStore } from '@/stores/auth';
import { useCallsStore } from '@/stores/calls';
import { useCitiesStore } from '@/stores/cities';
import { useGendersStore } from '@/stores/genders';
import { useLeadSourcesStore } from '@/stores/lead-sources';
import { usePatientsStore } from '@/stores/patients';
import { useProvincesStore } from '@/stores/provinces';
import { useTreatmentsStore } from '@/stores/treatments';
import { useUsersStore } from '@/stores/users';
import { onBeforeUnmount, onMounted, reactive, watch } from 'vue';

const filters = reactive({})

const store = usePatientsStore()
const provinces = useProvincesStore()
const leadSources = useLeadSourcesStore()
const cities = useCitiesStore()
const genders = useGendersStore()
const treatments = useTreatmentsStore()
const auth = useAuthStore()
const callStore = useCallsStore()
callStore.index()
const users = useUsersStore()
users.pagiantor.rows = 1000
users.index()

treatments.index()
provinces.index()
leadSources.index()

const handleSubmit = async () => {
    store.pagiantor.page = 1
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

onBeforeUnmount(() => {
    users.pagiantor.rows = 10
})
</script>

<style lang="scss" scoped></style>