<template>
    <form @submit.prevent="handleSubmit()" @keypress.enter.prevent="" class="flex w-full gap-4 [&>span]:flex-1">
        <FloatLabel variant="on" class="max-w-20">
            <InputText v-model="filters.id" class="ltr w-full" v-keyfilter.int />
            <label>{{ $t('id') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.name" class="w-full" />
            <label>{{ $t('name') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.mobile" class="ltr w-full" v-keyfilter.int />
            <label>{{ $t('mobile') }} </label>
        </FloatLabel>
        <FloatLabel variant="on">
            <InputText v-model="filters.email" class="ltr w-full" />
            <label>{{ $t('email') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <MultiSelect v-model="filters.roles" display="chip" :options="roles.items" :loading="roles.fetching"
                optionLabel="title" optionValue="id" :showToggleAll="false" fluid />
            <label> {{ $t('roles') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.status" :options="statuses" option-label="label" option-value="value" fluid
                show-clear checkmark />
            <label> {{ $t('status') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.created_at" selectionMode="range" :manualInput="false" class="rtl w-full"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new Date()" />
            <label> {{ $t('created_at') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.updated_at" selectionMode="range" :manualInput="false" class="rtl w-full"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new Date()" />
            <label> {{ $t('updated_at') }}</label>
        </FloatLabel>
        <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" class="w-28"
            :loading="store.fetching" />
    </form>
</template>

<script setup>
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
    store.filters = filters
    store.index()
}

watch(filters, () => {
    Object.entries(filters).forEach(([key, value]) => {
        if (value === null || value.length === 0) delete filters[key]
    })
}, { deep: true })

onMounted(() => {
    Object.assign(filters, store.filters)
    delete filters.query
})

onBeforeUnmount(() => store.filters = {})
</script>

<style lang="scss" scoped></style>