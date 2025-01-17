<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-wrap w-full gap-4 [&>span]:w-52">
        <FloatLabel variant="on">
            <InputText v-model="filters.id" fluid class="ltr" v-keyfilter.int />
            <label>{{ $t('id') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.name"  />
            <label>{{ $t('name') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.mobile" fluid class="ltr" v-keyfilter.int />
            <label>{{ $t('mobile') }} </label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.email" fluid class="ltr" />
            <label>{{ $t('email') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <MultiSelect v-model="filters.roles" display="chip" :options="roles.items" :loading="roles.fetching"
                optionLabel="title" optionValue="id" :showToggleAll="false" fluid />
            <label> {{ $t('roles') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.status" :options="statuses" option-label="label" option-value="value" fluid
                show-clear  />
            <label> {{ $t('status') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.created_at" selectionMode="range" :manualInput="false" fluid class="ltr"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new MyDate()" />
            <label> {{ $t('created_at') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.updated_at" selectionMode="range" :manualInput="false" fluid class="ltr"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new MyDate()" />
            <label> {{ $t('updated_at') }}</label>
        </FloatLabel>
        <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" class="w-28 mr-auto"
            :loading="store.fetching" />
    </form>
</template>

<script setup>
import MyDate from '../utils/MyDate'

import { useRolesStore } from '@/stores/roles';
import { useUsersStore } from '@/stores/users';
import { inject, onBeforeUnmount, onMounted, reactive, watch } from 'vue';

const { t } = inject('service')

const statuses = reactive([
    { label: t('deactive'), value: false },
    { label: t('active'), value: true }
])

const filters = reactive({})

const roles = useRolesStore()

const store = useUsersStore()

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

onMounted(() => Object.assign(filters, store.filters))

onBeforeUnmount(() => store.filters = {})
</script>

<style lang="scss" scoped></style>