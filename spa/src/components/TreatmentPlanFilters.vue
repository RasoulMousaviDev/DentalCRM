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
            <Select v-model="filters.visit_type" :options="['in-person', 'online']" :optionLabel="(opt) => $t(opt)"
                fluid show-clear />
            <label>{{ $t('visit-type') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <Select v-model="filters.payment_method" :options="['cash', 'installments']" :optionLabel="(opt) => $t(opt)"
                fluid show-clear />
            <label>{{ $t('payment-method') }}</label>
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
            <label> {{ $t('status') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.created_at" selectionMode="range" :manualInput="false" class="ltr w-full"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new MyDate()" />
            <label> {{ $t('created_at') }}</label>
        </FloatLabel>
        <FloatLabel variant="on">
            <DatePicker v-model="filters.updated_at" selectionMode="range" :manualInput="false" class="ltr w-full"
                showButtonBar dateFormat="yy/mm/dd" :max-date="new MyDate()" />
            <label> {{ $t('updated_at') }}</label>
        </FloatLabel>
        <Button icon="pi pi-search" :label="$t('search')" type="submit" severity="warn" class="w-28 shrink-0 mr-auto"
            :loading="store.fetching" />
    </form>
</template>

<script setup>
import MyDate from '../utils/MyDate'
import { useAuthStore } from '@/stores/auth';
import { useTreatmentPlansStore } from '@/stores/treatment-plans';
import { useUsersStore } from '@/stores/users';
import { inject, onBeforeUnmount, onMounted, reactive, watch } from 'vue';

const auth = useAuthStore()

const users = useUsersStore()
users.pagiantor.rows = 1000
users.index()

const { route } = inject('service')

const { patient = null } = route.query || {}

const filters = reactive({ patient })

const store = useTreatmentPlansStore()

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