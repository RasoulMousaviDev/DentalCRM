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
            <label>{{ $t('due_date') }}</label>
            <DatePicker v-model="date" class="w-full" :placeholder="$t('choose')"
                :inputClass="{ 'ltr': filters.due_date }" panelClass="ltr" dateFormat="yy/mm/dd"
                showButtonBar @clearClick="date = null" />
        </div>
        <div class="flex flex-col gap-2">
            <label> {{ $t('status') }}</label>
            <Select v-model="filters.status" :options="statuses" optionValue="value" fluid 
                :placeholder="$t('choose')" show-clear>
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="getTag(value)" />
                </template>
                <template #option="{ option: { value, severity } }">
                    <Tag :value="$t(value)" :severity="severity" class="text-xs" />
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
import { useFollowupsStore } from '@/stores/followups';
import { computed, inject, onMounted, reactive, watch } from 'vue';

const { popover, t } = inject('service')

const statuses = reactive([
    { value: 'pending', severity: 'warn' },
    { value: 'done', severity: 'success' },
    { value: 'missed', severity: 'danger' },
])

const filters = reactive({})

const date = computed({
    get: () => {
        if (filters.due_date) {
            const d = filters.due_date.split('/');
            return new Date(d[0], d[1] - 1, d[2]);
        }
        return null
    },
    set: (v) => {
        if (v) filters.due_date = [
            v.getFullYear(),
            ('0' + (v.getMonth() + 1)).slice(-2),
            ('0' + v.getDate()).slice(-2)
        ].join('/')
        else delete filters.due_date
    }
})

const followups = useFollowupsStore()

const handleSubmit = async () => {
    popover.value.hide()
    followups.filters = filters
    followups.index()
}

const clearFilters = () => {
    popover.value.hide()
    followups.filters = {}
    followups.index()
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
    Object.assign(filters, followups.filters)
    delete filters.query
})
</script>

<style lang="scss" scoped></style>