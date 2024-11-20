<template>
    <form @submit.prevent="handleSubmit()" class="flex w-full gap-4 [&>span]:flex-1">
        <FloatLabel variant="on" class="max-w-20">
            <InputText v-model="filters.id" fluid class="ltr" v-keyfilter.int />
            <label>{{ $t('id') }}</label>
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
            <DatePicker v-model="due_date" selectionMode="range" :manualInput="false" fluid class="ltr" showButtonBar
                dateFormat="yy/mm/dd" />
            <label>{{ $t('due-date') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.treatment" :options="treatments.items" :loading="treatments.fetching"
                optionLabel="title" optionValue="id" fluid show-clear />
            <label>{{ $t('treatment') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.status" :options="store.statuses" optionValue="id" fluid show-clear>
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="store.statuses.find(({ id }) => value == id)" />
                </template>
                <template #option="{ option: { value, severity } }">
                    <Tag :value="$t(value)" :severity="severity" class="text-xs" />
                </template>
            </Select>
            <label> {{ $t('status') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.created_at" selectionMode="range" :manualInput="false" fluid class="ltr"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new Date()" />
            <label> {{ $t('created_at') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.updated_at" selectionMode="range" :manualInput="false" fluid class="ltr"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new Date()" />
            <label> {{ $t('updated_at') }}</label>
        </FloatLabel>
        <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" class="w-32"
            :loading="store.fetching" />
    </form>
</template>

<script setup>
import { useAppointmentsStore } from '@/stores/appointments';
import { useTreatmentsStore } from '@/stores/treatments';
import { inject, onBeforeUnmount, onMounted, reactive, watch } from 'vue';

const { route } = inject('service')

const { patient = null } = route.query || {}

const filters = reactive({ patient })

const treatments = useTreatmentsStore()
treatments.index()

const store = useAppointmentsStore()

const handleSubmit = async () => {
    store.filters = filters
    store.index()
}

watch(filters, () => {
    Object.entries(filters).forEach(([key, value]) => {
        if (value === null || value.length === 0) delete filters[key]
    })
}, { deep: true })

onMounted(() => Object.assign(filters, store.filters))

onBeforeUnmount(() => store.filters = {})
</script>

<style lang="scss" scoped></style>