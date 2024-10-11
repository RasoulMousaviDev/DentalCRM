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
            <label>{{ $t('amount') }}</label>
            <InputGroup class="ltr">
                <InputGroupAddon>{{ $t('toman') }}</InputGroupAddon>
                <InputNumber v-model="filters.amount" class="w-full" />
            </InputGroup>
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('payment-date') }}</label>
            <DatePicker v-model="payment_date" class="w-full" :placeholder="$t('choose')"
                :inputClass="{ 'ltr': filters.payment_date }" panelClass="ltr" dateFormat="yy/mm/dd" showButtonBar
                @clearClick="payment_date = null" />
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('refund-date') }}</label>
            <DatePicker v-model="refund_date" class="w-full" :placeholder="$t('choose')"
                :inputClass="{ 'ltr': filters.refund_date }" panelClass="ltr" dateFormat="yy/mm/dd" showButtonBar
                @clearClick="refund_date = null" />
        </div>
        <div class="flex flex-col gap-2">
            <label> {{ $t('status') }}</label>
            <Select v-model="filters.status" :options="statuses" optionValue="value" fluid checkmark
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
import { useDepositsStore } from '@/stores/deposits';
import { computed, inject, onMounted, reactive, watch } from 'vue';

const { popover, t } = inject('service')

const statuses = reactive([
    { value: 'paid', severity: 'success' },
    { value: 'refunded', severity: 'danger' },

])

const filters = reactive({})

const date = (key) => ({
    get: () => {
        if (filters[key]) {
            return new Date(...filters[key].split('/'));
        }
        return null
    },
    set: (v) => {
        if (v) filters[key] = [
            v.getFullYear(),
            ('0' + (v.getMonth() + 1)).slice(-2),
            ('0' + v.getDate()).slice(-2)
        ].join('/')
        else delete filters.due_date
    }
})
const payment_date = computed(date("payment_date"))
const refund_date = computed(date("refund_date"))

const deposits = useDepositsStore()

const handleSubmit = async () => {
    popover.value.hide()
    deposits.filters = filters
    deposits.index()
}

const clearFilters = () => {
    popover.value.hide()
    deposits.filters = {}
    deposits.index()
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
    Object.assign(filters, deposits.filters)
    delete filters.query
})
</script>

<style lang="scss" scoped></style>