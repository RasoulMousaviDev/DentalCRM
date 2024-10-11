<template>
    <form @submit.prevent="handleSubmit()" @keypress.enter.prevent="" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label>{{ $t('name') }}</label>
            <InputText v-model="filters.name" class="w-full" />
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('mobile') }} </label>
            <InputText v-model="filters.mobile" class="ltr w-full" />
        </div>
        <div class="flex flex-col gap-2">
            <label>{{ $t('email') }}</label>
            <InputText v-model="filters.email" class="ltr w-full" />
        </div>
        <div class="flex flex-col gap-2">
            <label> {{ $t('roles') }}</label>
            <MultiSelect v-model="filters.roles" display="chip" :options="roles.items" :loading="roles.fetching"
                optionLabel="title" optionValue="id" :showToggleAll="false" fluid />
        </div>
        <div class="flex justify-between items-center mt-2">
            <label> {{ $t('status') }}</label>
            <SelectButton v-model="filters.status" :options="statuses" option-label="label" option-value="value"
                class="ltr" />
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
import { useRolesStore } from '@/stores/roles';
import { useUsersStore } from '@/stores/users';
import { inject, onMounted, reactive, watch } from 'vue';

const { popover, t } = inject('service')

const statuses = reactive([
    { label: t('deactive'), value: false },
    { label: t('active'), value: true }
])

const filters = reactive({})

const roles = useRolesStore()
roles.index()

const users = useUsersStore()

const handleSubmit = async () => {
    popover.value.hide()
    users.filters = filters
    users.index()
}

const clearFilters = () => {
    popover.value.hide()
    users.filters = {}
    users.index()
}

watch(filters, () => {
    Object.entries(filters).forEach(([key, value]) => {
        if (value === null || value.length === 0) delete filters[key]
    })
}, { deep: true })

onMounted(() => {
    Object.assign(filters, users.filters)
    delete filters.query
})

</script>

<style lang="scss" scoped></style>