<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-8 has-[small]:gap-3 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputText v-model="form.name" :invalid="errors.name" fluid />
                <label>{{ $t('name') }}</label>
            </FloatLabel>
            <small v-if="errors.name" v-text="errors.name[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputText v-model="form.mobile" :invalid="errors.mobile" class="ltr" fluid v-keyfilter.int />
                <label>{{ $t('mobile') }}</label>
            </FloatLabel>
            <small v-if="errors.mobile" v-text="errors.mobile[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputText v-model="form.email" :invalid="errors.email" class="ltr" fluid />
                <label>{{ $t('email') }}</label>
            </FloatLabel>
            <small v-if="errors.email" v-text="errors.email[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <MultiSelect v-model="form.roles" display="chip" :options="roles.items" :loading="roles.fetching"
                    optionLabel="title" optionValue="id" :showToggleAll="false" :invalid="errors.roles" fluid />
                <label> {{ $t('roles') }}</label>
            </FloatLabel>
            <small v-if="errors.roles" v-text="errors.roles[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between items-center mt-2">
            <label> {{ $t('status') }}</label>
            <ToggleButton v-model="form.status" class="w-20" :onLabel="$t('active')" :offLabel="$t('deactive')" />
        </div>
        <div class="flex justify-between gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useRolesStore } from '@/stores/roles';
import { useUsersStore } from '@/stores/users';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast, } = inject('service')

const dialogRef = inject('dialogRef')
const { user } = dialogRef?.value.data || {}

const form = reactive({ name: '', mobile: '', email: '', roles: [], status: true })
const errors = ref({})
const loading = ref(false)

const roles = useRolesStore()
roles.index()

const users = useUsersStore()

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