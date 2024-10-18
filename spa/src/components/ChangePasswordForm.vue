<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('current-password') }}</label>
            <Password v-model="form.current_password" dir="ltr" toggleMask fluid :feedback="false"
                class="[&_input]:has-[+small]:!border-red-500" />
            <small v-if="errors.current_password" v-text="errors.current_password[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('new-password') }}</label>
            <Password v-model="form.new_password" dir="ltr" toggleMask fluid :feedback="false"
                class="[&_input]:has-[+small]:!border-red-500" />
            <small v-if="errors.new_password" v-text="errors.new_password[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('repeat-new-password') }}</label>
            <Password v-model="form.new_password_confirmation" dir="ltr" toggleMask fluid :feedback="false"
                class="[&_input]:has-[+small]:!border-red-500" />
        </div>

        <div class="flex justify-between gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useCookie } from '@/composables/cookie';
import { usePasswordStore } from '@/stores/password';
import { inject, reactive, ref } from 'vue';

const { toast, router } = inject('service')

const dialogRef = inject('dialogRef')

const form = reactive({ current_password: '', new_password: '', new_password_confirmation: '' })
const errors = ref({})
const loading = ref(false)

const password = usePasswordStore()

const handleSubmit = async () => {
    errors.value = {}
    loading.value = true

    const { status, statusText, data } = await password.change(form)

    loading.value = false

    if (statusText === 'OK') {
        dialogRef.value.close();
        toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 5000 });
    } else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}


</script>

<style lang="scss" scoped></style>