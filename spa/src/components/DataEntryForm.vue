<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex justify-between items-center">
            <label class="has-[+*+small]:text-red-500"> {{ $t('excel-file') }}</label>
            <input ref="file" type="file" hidden @input="chooseFile" @change="chooseFile">
            <Button v-if="form.file" icon="pi pi-times" :label="form.file.name.slice(-24)" outlined severity="info"  @click="() => file.click()"/>
            <Button v-else icon="pi pi-file-excel" :label="$t('choose-file')" severity="info" @click="() => file.click()" />
            <small v-if="errors.file" v-text="errors.file[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 w-44">
            <label class="has-[+*+small]:text-red-500"> {{ $t('consultants') }}</label>
            <MultiSelect v-model="form.roles" display="chip" :options="users.items" :loading="users.fetching"
                optionLabel="name" optionValue="id" fluid filter class="has-[+small]:!border-red-500 overflow-x-auto"
                panelClass="[&>*>.p-checkbox]:!mr-0 [&>*>.p-checkbox]:ml-2" />
            <small v-if="errors.roles" v-text="errors.roles[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useUsersStore } from '@/stores/users';
import { usePatientsStore } from '@/stores/patients';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast, } = inject('service')

const dialogRef = inject('dialogRef')
const { user } = dialogRef.value.data || {}

const file = ref()

const form = reactive({ consultants: [], file: null })
const errors = ref({})
const loading = ref(false)

const users = useUsersStore()
users.index()

const patients = usePatientsStore()

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (user)
        result = await users.update(user.id, form)
    else
        result = await users.store(form)

    const { status, statusText, data } = result

    loading.value = false

    if (statusText === 'OK')
        dialogRef.value.close();
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}

const chooseFile = (e) => {
    form.file = e.target.files[0]
}

watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})

onMounted(() => {
    if (user)
        Object.keys(form).forEach((key) => form[key] = user[key])
})
</script>

<style lang="scss" scoped></style>