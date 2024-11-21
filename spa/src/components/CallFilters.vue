<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-wrap w-full gap-4 [&>span]:w-52">
        <template v-if="$route.name != 'Patient'">
            <FloatLabel variant="on">
                <InputText v-model="filters.patient" fluid class="ltr" v-keyfilter.int />
                <label>{{ $t('patient-id') }}</label>
            </FloatLabel>
            <FloatLabel variant="on">
                <InputText v-model="filters.user" fluid />
                <label>{{ $t('consultant') }}</label>
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
            <InputText v-model="filters.mobile" type="phone" class="ltr w-full" />
            <label>{{ $t('mobile') }} </label>
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
            <label> {{ $t('call-status') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.created_at" selectionMode="range" :manualInput="false" fluid class="ltr"
                showButtonBar dateFormat="yy/mm/dd" />
            <label> {{ $t('created_at') }}</label>
        </FloatLabel>
        <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" class="w-32 mr-auto self-center"
            :loading="store.fetching" />
    </form>
</template>

<script setup>
import { useCallsStore } from '@/stores/calls';
import { inject, onBeforeUnmount, onMounted, reactive, watch } from 'vue';

const { route } = inject('service')

const { patient = null } = route.query || {}

const filters = reactive({ patient })

const store = useCallsStore()

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