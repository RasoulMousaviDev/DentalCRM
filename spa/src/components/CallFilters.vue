<template>
    <form @submit.prevent="handleSubmit()" @keypress.enter.prevent="" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label>{{ $t('firstname') }}</label>
            <InputText v-model="filters.firstname" fluid />
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('lastname') }}</label>
            <InputText v-model="filters.lastname" fluid />
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('mobile') }} </label>
            <InputText v-model="filters.mobile" type="phone" class="ltr w-full" />
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('date') }}</label>
            <DatePicker v-model="date" class="w-full" :placeholder="$t('choose')"
                :inputClass="{ 'ltr': filters.created_at }" panelClass="ltr" dateFormat="yy/mm/dd"
                showButtonBar @clearClick="date = null" />
        </div>
        <div class="flex flex-col gap-2">
            <label> {{ $t('status') }}</label>
            <Select v-model="filters.status" :options="callStatuses.items" :loading="callStatuses.fetching"
                optionValue="id" fluid checkmark :placeholder="$t('choose')">
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="callStatuses.items.find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
        </div>
        <div class="flex gap-2 mt-4">
            <Button :label="$t('back')" severity="secondary" class="ml-auto" @click="popover.hide()" />
            <Button v-if="Object.keys(filters).length > 0" icon="pi pi-filter-slash" :label="$t('clear')"
                severity="warn" outlined @click="clearFilters()" />
            <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" />
        </div>
    </form>
</template>

<script setup>
import { useCallStatuesStore } from '@/stores/call-statuses';
import { useCallsStore } from '@/stores/calls';
import { computed, inject, onMounted, reactive, watch } from 'vue';

const { popover, t } = inject('service')

const statuses = reactive([
    { value: 'pending', severity: 'warn' },
    { value: 'visited', severity: 'success' },
    { value: 'missed', severity: 'danger' },
    { value: 'canceled', severity: 'info' },
])

const filters = reactive({})

const date = computed({
    get: () => {
        if (filters.created_at) {
            const d = filters.created_at.split('/');
            return new Date(d[0], d[1] - 1, d[2]);
        }
        return null
    },
    set: (v) => {
        if (v) filters.created_at = [
            v.getFullYear(),
            ('0' + (v.getMonth() + 1)).slice(-2),
            ('0' + v.getDate()).slice(-2)
        ].join('/')
        else delete filters.created_at
    }
})

const callStatuses = useCallStatuesStore()
callStatuses.index()

const calls = useCallsStore()

const handleSubmit = async () => {
    popover.value.hide()
    calls.filters = filters
    calls.index()
}

const clearFilters = () => {
    popover.value.hide()
    calls.filters = {}
    calls.index()
}

const getTag = (value) => {
    const item = Object.assign({}, statuses.find((item) => item.value == value))
    item.value = t(item.value)
    return item
}

watch(filters, () => {
    Object.entries(filters).forEach(([key, value]) => {
        if (value === null || value.length === 0) delete filters[key]
    })
}, { deep: true })

onMounted(() => {
    Object.assign(filters, calls.filters)
    delete filters.query
})
</script>

<style lang="scss" scoped></style>