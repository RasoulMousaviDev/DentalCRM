<template>
    <form @submit.prevent="handleSubmit()" @keypress.enter.prevent="" class="grid grid-cols-2 gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label>{{ $t('firstname') }}</label>
            <InputText v-model="filters.firstname" class="w-full" />
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('lastname') }}</label>
            <InputText v-model="filters.lastname" class="w-full" />
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('birthday') }}</label>
            <DatePicker v-model="date" class="w-full" :placeholder="$t('choose')" :inputClass="{ 'ltr': filters.birthday }"
                panelClass="ltr -translate-x-10" dateFormat="yy/mm/dd" />
        </div>
        <div class="flex flex-col gap-2 grow">
            <label>{{ $t('gender') }}</label>
            <Select v-model="filters.gender" :options="genders" :optionLabel="(opt) => $t(opt)" fluid checkmark
                :placeholder="$t('choose')" />
        </div>

        <div class="flex flex-col gap-2 col-span-2">
            <label>
                {{ $t('mobile') }}
            </label>
            <InputText v-model="filters.mobile" class="ltr w-full" />
        </div>

        <div class="flex flex-col gap-2">
            <label> {{ $t('province') }}</label>
            <Select v-model="filters.province" :options="provinces.items" :loading="provinces.fetching" optionLabel="title"
                optionValue="id" fluid checkmark :placeholder="$t('choose')" />
        </div>
        <div class="flex flex-col gap-2">
            <label> {{ $t('city') }}</label>
            <Select v-model="filters.city" :options="cities.items" :loading="cities.fetching"
                :emptyMessage="$t('first-select-province')" optionLabel="title" optionValue="id" fluid checkmark
                :placeholder="$t('choose')" />
        </div>
        <div class="flex flex-col gap-2">
            <label> {{ $t('lead_source') }}</label>
            <Select v-model="filters.lead_source" :options="leadSources.items" :loading="leadSources.fetching"
                optionLabel="title" optionValue="id" fluid checkmark :placeholder="$t('choose')" />
        </div>
        <div class="flex flex-col gap-2">
            <label> {{ $t('status') }}</label>
            <Select v-model="filters.status" :options="statuses.items" :loading="statuses.fetching" optionValue="id" fluid
                checkmark>
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="statuses.items.find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
        </div>
        <div class="flex flex-col gap-2 col-span-2">
            <label> {{ $t('desc') }}</label>
            <Textarea v-model="filters.desc" fluid rows="5" cols="30" />
        </div>
        <div class="flex justify-between col-span-2 gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" class="ml-auto" @click="popover.hide()" />
            <Button v-if="Object.keys(filters).length > 0" icon="pi pi-filter-slash" :label="$t('clear')"
                severity="warn" outlined @click="clearFilters()" />
            <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" />
        </div>
    </form>
</template>

<script setup>
import { useCitiesStore } from '@/stores/cities';
import { useLeadSourcesStore } from '@/stores/lead-sources';
import { usePatientStatuesStore } from '@/stores/patient-statuses';
import { usePatientsStore } from '@/stores/patients';
import { useProvincesStore } from '@/stores/provinces';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { popover } = inject('service')

const filters = reactive({})

const date = computed({
    get: () => {
        if (filters.birthday) {
            const d = filters.birthday.split('/');
            return new Date(d[0], d[1] - 1, d[2]);
        }
        return null
    },
    set: (v) => filters.birthday = [
        v.getFullYear(),
        ('0' + (v.getMonth() + 1)).slice(-2),
        ('0' + v.getDate()).slice(-2)
    ].join('/')
})

const provinces = useProvincesStore()
const statuses = usePatientStatuesStore()
const leadSources = useLeadSourcesStore()

statuses.index()
provinces.index()
leadSources.index()

const genders = reactive(['male', 'female'])

const patients = usePatientsStore()

const handleSubmit = async () => {
    popover.value.hide()
    patients.filters = filters
    patients.index()
}

const clearFilters = () => {
    popover.value.hide()
    patients.filters = {}
    patients.index()
}

watch(filters, () => {
    Object.entries(filters).forEach(([key, value]) => {
        if (value === null || value.length === 0) delete filters[key]
    })
}, { deep: true })

const cities = useCitiesStore()
watch(() => filters.province, (v) => { cities.index(v) })

onMounted(() => {
    Object.assign(filters, patients.filters)
    delete filters.query
})

</script>

<style lang="scss" scoped></style>