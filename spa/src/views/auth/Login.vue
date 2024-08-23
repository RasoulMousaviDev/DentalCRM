<template>
    <AuthLayout :title="$t('welcome')" :sub-title="$t('login-to-continue')">
        <form @submit.prevent="handleSubmit()" class="flex flex-col w-full md:w-[30rem]">
            <label class="text-surface-900 dark:text-surface-0 text-xl font-medium has-[+*+small]:text-red-500">
                {{ $t('phone-or-email') }}
            </label>
            <InputText v-model="emailOrPhone" :name="form.type" dir="ltr" class="w-full mt-2 mb-1 has-[+small]:!border-red-500" />
            <small v-if="errors.type" v-text="errors.type[0]" class="text-red-500" />
            <small v-else-if="errors.phone" v-text="errors.phone[0]" class="text-red-500" />
            <small v-else-if="errors.email" v-text="errors.email[0]" class="text-red-500" />

            <label class="text-surface-900 dark:text-surface-0 font-medium text-xl mb-2 mt-7 has-[+*+small]:text-red-500">
                {{ $t('password') }}
            </label>
            <Password v-model="form.password" dir="ltr" toggleMask fluid :feedback="false"
                class="[&_input]:has-[+small]:!border-red-500 mb-1" />
            <small v-if="errors.password" v-text="errors.password[0]" class="text-red-500" />

            <Button :label="$t('forgot-password') + '؟'" class="!px-0 my-6 self-end" link as="router-link"
                :to="{ name: 'ForgotPassword' }" />

            <Button type="submit" :label="$t('login')" class="w-full" :loading="loading" />
        </form>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/Auth.vue';
import { useAuthStore } from '@/stores/auth';
import { computed, inject, reactive, ref, watch } from 'vue';

const { router, toast } = inject('service')

const emailOrPhone = ref('')
const form = reactive({})
const errors = ref({})
const loading = ref(false)
const store = useAuthStore()

const handleSubmit = async () => {
    loading.value = true

    const { statusText, status, data } = await store.login(form)

    loading.value = false

    if (statusText == 'OK')
        router.push('/dashboard');
    else if (status === 422) 
        errors.value = data.errors
    else if (status === 400 || status === 429) 
        toast.add({ severity: 'error', summary: 'Error', detail:  data.message, life: 5000 });
    
}

watch(emailOrPhone, (v) => {
    delete errors.value.type
    delete form.email
    delete form.phone
    delete form.type

    let type = ''
    if (v.startsWith('09')) type = 'phone'
    else if (v.includes('@')) type = 'email'

    if(type){
        form.type = type
        form[type] = v
    }
})

watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})
</script>

<style lang="scss"></style>
