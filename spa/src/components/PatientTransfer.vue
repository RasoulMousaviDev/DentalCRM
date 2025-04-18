<template>
    <form @submit.prevent="handleSubmit()"
        class="grid grid-cols-2 gap-x-4 gap-y-8 [&_small]:-mb-6 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.from"
                    :options="users.items.filter((user) => user.roles.some((role) => role.name == 'phone-consultant'))"
                    option-label="name" option-value="id" fluid :invalid="errors.from" />
                <label>{{ $t('from-consultant') }}</label>
            </FloatLabel>
            <small v-if="errors.from" v-text="errors.from[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.to"
                    :options="users.items.filter((user) => user.roles.some((role) => role.name == 'phone-consultant'))"
                    option-label="name" option-value="id" fluid :invalid="errors.to" />
                <label>{{ $t('to-consultant') }}</label>
            </FloatLabel>
            <small v-if="errors.to" v-text="errors.to[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <MultiSelect v-model="form.status" :options="store.statuses" optionValue="id" fluid show-clear>
                    <template #value="{ value }">
                        <Tag v-for="s in value" class="text-xs" v-bind="store.statuses.find(({ id }) => s == id)" />
                    </template>
                    <template #option="{ option }">
                        <Tag v-bind="option" class="text-xs" />
                    </template>
                </MultiSelect>
                <label> {{ $t('status') }}</label>
            </FloatLabel>
            <small v-if="errors.statuses" v-text="errors.statuses[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <MultiSelect v-model="form.lead_source" :options="leadSources.items" optionLabel="title"
                    optionValue="id" fluid show-clear />
                <label> {{ $t('lead-source') }}</label>
            </FloatLabel>
            <small v-if="errors.lead_source" v-text="errors.lead_source[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputNumber v-model="form.count" fluid :invalid="errors.count" class="ltr" />
                <label>{{ $t('count') }}</label>
            </FloatLabel>
            <small v-if="errors.count" v-text="errors.count[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <DatePicker v-model="form.created_at" selectionMode="range" :manualInput="false" class="ltr w-full"
                    showButtonBar dateFormat="yy/mm/dd" :max-date="new MyDate()" :invalid="errors.created_at" />
                <label> {{ $t('created_at') }}</label>
            </FloatLabel>
            <small v-if="errors.created_at" v-text="errors.created_at[0]" class="text-red-500" />
        </div>
        <label class="flex items-center gap-3 p-inputtext cursor-pointer">
            <Checkbox v-model="form.no_pending_follow_up" binary />
            <span> {{ $t('no-pending-follow-up') }} </span>
        </label>
        <label class="flex items-center gap-3 p-inputtext cursor-pointer">
            <Checkbox v-model="form.no_phone_consultant" binary />
            <span> {{ $t('no-phone-consultant') }} </span>
        </label>
        <div class="flex gap-2 mt-4 col-span-2">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button class="mr-auto" icon="pi pi-history"  :label="$t('history')" severity="warn" @click="dialogRef.close()" />
            <Button icon="pi pi-arrow-right-arrow-left" :label="$t('transfer')" severity="info" type="submit"
                :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useLeadSourcesStore } from '@/stores/lead-sources'
import { usePatientsStore } from '@/stores/patients'
import { useUsersStore } from '@/stores/users'
import MyDate from '@/utils/MyDate'
import { computed, inject, reactive, ref, watch } from 'vue'

const dialogRef = inject('dialogRef')

const { toast } = inject('service')

const form = reactive({
    from: null,
    to: null,
    status: null,
    lead_source: null,
    count: null,
    created_at: null,
    no_pending_follow_up: null
})
const errors = ref({})
const loading = ref(false)

const store = usePatientsStore()
const leadSources = useLeadSourcesStore()
const users = useUsersStore()

const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await store.transfer(form)

    loading.value = false

    if (statusText === 'OK') {
        toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 5000 });
        dialogRef.value.close();
    }
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });
}

watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})

</script>

<style lang="scss" scoped></style>