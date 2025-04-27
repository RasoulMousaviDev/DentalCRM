<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-wrap w-full gap-4 [&>span]:w-52">
        <template v-if="$route.name != 'Patient'">
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
            <DatePicker v-model="filters.due_date" selectionMode="range" :manualInput="false" fluid class="ltr"
                showButtonBar dateFormat="yy/mm/dd" />
            <label>{{ $t('due-date') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.treatment" :options="treatments.items" :loading="treatments.fetching"
                optionLabel="title" optionValue="id" fluid show-clear />
            <label>{{ $t('treatment') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <MultiSelect v-model="filters.status" :options="store.statuses" optionValue="id" fluid show-clear>
                <template #value="{ value }">
                    <Tag v-for="s in value" class="text-xs" v-bind="store.statuses.find(({ id }) => s == id)" />
                </template>
                <template #option="{ option: { value, severity } }">
                    <Tag :value="$t(value)" :severity="severity" class="text-xs" />
                </template>
            </MultiSelect>
            <label> {{ $t('status') }}</label>
        </FloatLabel>
        <label v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)"
            class="flex items-center gap-3 w-52 p-inputtext cursor-pointer">
            <Checkbox v-model="filters.no_phone_consultant" binary />
            <span> {{ $t('no-phone-consultant') }} </span>
        </label>
        <label class="flex items-center gap-3 w-52 p-inputtext cursor-pointer">
            <Checkbox v-model="filters.no_treatment_plan" binary />
            <span> {{ $t('no-treatment-plan') }} </span>
        </label>
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
        <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" class="w-32 mr-auto"
            :loading="store.fetching" />
    </form>
</template>

<script setup>
import MyDate from '../utils/MyDate'
import { useAppointmentsStore } from '@/stores/appointments';
import { useAuthStore } from '@/stores/auth';
import { useTreatmentsStore } from '@/stores/treatments';
import { useUsersStore } from '@/stores/users';
import { inject, onBeforeUnmount, onMounted, reactive, watch } from 'vue';

const auth = useAuthStore()

const users = useUsersStore()
users.pagiantor.rows = 1000
users.index()

const { route } = inject('service')

const { patient = null } = route.query || {}

const filters = reactive({ patient })

const treatments = useTreatmentsStore()
treatments.index()

const store = useAppointmentsStore()

const handleSubmit = async () => {
    store.pagiantor.page = 1
    store.filters = filters
    store.index()
}

watch(() => auth.user.role, (v) => {
    if (v?.name == 'appointment')
        filters.status = 6
}, { immediate: true })

watch(filters, () => {
    Object.entries(filters).forEach(([key, value]) => {
        if (value === null || value.length === 0) delete filters[key]
    })
}, { deep: true })

onMounted(() => Object.assign(filters, store.filters))

onBeforeUnmount(() => store.filters = {})
</script>

<style lang="scss" scoped></style>