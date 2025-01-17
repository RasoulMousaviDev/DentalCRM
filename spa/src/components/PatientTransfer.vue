<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-8 [&_small]:-mb-6 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.from"
                    :options="users.items.filter((user) => user.roles.some((role) => role.name == 'phone-consultant'))"
                    option-label="name" option-value="id" fluid show-clear />
                <label>{{ $t('from') }}</label>
            </FloatLabel>
            <small v-if="errors.from" v-text="errors.from[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.to"
                    :options="users.items.filter((user) => user.roles.some((role) => role.name == 'phone-consultant'))"
                    option-label="name" option-value="id" fluid show-clear />
                <label>{{ $t('to') }}</label>
            </FloatLabel>
            <small v-if="errors.to" v-text="errors.to[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputNumber v-model="form.count" fluid />
                <label>{{ $t('count') }}</label>
            </FloatLabel>
            <small v-if="errors.count" v-text="errors.count[0]" class="text-red-500" />
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
            <small v-if="errors.status" v-text="errors.status[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <DatePicker v-model="form.created_at" selectionMode="range" :manualInput="false" class="ltr w-full"
                    showButtonBar dateFormat="yy/mm/dd" :max-date="new MyDate()" />
                <label> {{ $t('created_at') }}</label>
            </FloatLabel>
            <small v-if="errors.created_at" v-text="errors.created_at[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between gap-2 mt-4">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { usePatientsStore } from '@/stores/patients'
import { useUsersStore } from '@/stores/users'
import { computed, inject, reactive, ref, watch } from 'vue'

const dialogRef = inject('dialogRef')

const { toast } = inject('service')

const form = reactive({ from: null, to: null, status: null, count: null, created_at: null })
const errors = ref({})
const loading = ref(false)

const store = usePatientsStore()
const users = useUsersStore()
users.pagiantor.rows = 1000
users.index()

const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await store.transfer(form)

    loading.value = false

    if (statusText === 'OK'){
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