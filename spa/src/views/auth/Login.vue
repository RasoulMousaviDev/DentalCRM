<template>
    <AuthLayout v-bind="header">
        <Transition name="fade" mode="out-in" @beforeEnter="header = headers[state]">
            <template v-if="state === 0">
                <form @submit.prevent="handleSubmit()" class="flex flex-col w-full md:w-[30rem]">
                    <label class="text-surface-900 dark:text-surface-0 text-xl font-medium has-[+*+small]:text-red-500">
                        {{ $t('mobile-or-email') }}
                    </label>
                    <InputText v-model="emailOrMobile" :name="form.type"
                        class="ltr w-full mt-2 mb-1 has-[+small]:!border-red-500" />
                    <small v-if="errors.type" v-text="errors.type[0]" class="text-red-500" />
                    <small v-else-if="errors.mobile" v-text="errors.mobile[0]" class="text-red-500" />
                    <small v-else-if="errors.email" v-text="errors.email[0]" class="text-red-500" />

                    <label
                        class="text-surface-900 dark:text-surface-0 font-medium text-xl mb-2 mt-7 has-[+*+small]:text-red-500">
                        {{ $t('password') }}
                    </label>
                    <Password v-model="form.password" toggleMask fluid :feedback="false"
                        class="ltr [&_input]:has-[+small]:!border-red-500 mb-1" />
                    <small v-if="errors.password" v-text="errors.password[0]" class="text-red-500" />

                    <div class="flex justify-between">
                        <Button :label="$t('login-by-otp')" class="!px-0 my-6" icon="pi pi-chevron-left !text-xs" link @click="state = 1" />
                        <Button :label="$t('forgot-password') + '؟'" class="!px-0 my-6 self-end" link as="router-link"
                            :to="{ name: 'ForgotPassword' }" />
                    </div>

                    <Button type="submit" :label="$t('login')" class="w-full" :loading="loading.login" />
                </form>
            </template>
            <template v-else-if="state === 1">
                <form @submit.prevent="generateCode()" class="flex flex-col w-full md:w-[30rem]">
                    <label class="text-surface-900 dark:text-surface-0 text-xl font-medium has-[+*+small]:text-red-500">
                        {{ $t('mobile') }}
                    </label>
                    <InputText v-model="form.mobile" name="mobile" dir="ltr"
                        class="w-full mt-2 mb-1 has-[+small]:!border-red-500" />
                    <small v-if="errors.mobile" v-text="errors.mobile[0]" class="text-red-500" />

                    <Button :label="$t('login-by-password')" class="!px-0 mt-6 self-start" icon="pi pi-chevron-left !text-xs" link @click="state = 0" />

                    <Button :label="$t('confirmation')" class="w-full mt-8" type="submit"
                        :loading="loading.generating" />
                </form>
            </template>
            <template v-else-if="state === 2">
                <form @submit.prevent="verfiyCode()" class="flex flex-col items-center w-full md:w-[30rem]">
                    <InputOtp v-model="form.code" :length="6" style="gap: 0" dir="ltr" integerOnly>
                        <template #default="{ attrs, events, index }">
                            <input type="tel" v-bind="attrs" v-on="events" class="custom-otp-input" :autofocus="index == 0 || null" />
                            <div v-if="index === 3" class="px-5">
                                <i class="pi pi-minus" />
                            </div>
                        </template>
                    </InputOtp>

                    <div class="flex justify-between mt-12 self-stretch">
                        <Button :label="countdown.label" link class="!p-0" :loading="loading.generating"
                            :disabled="countdown.active" @click="generateCode()" />
                        <Button :label="$t('confirmation')" type="submit" :loading="loading.verifing"
                            :disabled="form.code?.length < 6" />
                    </div>
                </form>
            </template>
        </Transition>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/Auth.vue';
import { useAuthStore } from '@/stores/auth';
import { useOTPCodeStore } from '@/stores/otp-code';
import { computed, inject, reactive, ref, watch } from 'vue';

const { router, toast, t } = inject('service')

const state = ref(1)

const headers = reactive([
    { title: t('welcome'), subTitle: t('login-to-continue') },
    { title: t('welcome'), subTitle: t('login-to-continue') },
    { title: t('enter-otp-code'), subTitle: '', back: () => state.value = 1 },
])
const header = ref(headers[0])
const emailOrMobile = ref('')
const form = reactive({})
const errors = ref({})
const loading = reactive({ login: false, generating: false, verifing: false })

const store = useAuthStore()

const handleSubmit = async () => {
    loading.login = true

    const { statusText, status, data } = await store.login(form)

    loading.login = false

    if (statusText == 'OK')
        router.push('/');
    else if (status === 422)
        errors.value = data.errors
    else if (status === 400 || status === 429)
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}

const otpCode = useOTPCodeStore()

const generateCode = async () => {
    form.type = 'mobile'

    loading.generating = true

    const { status, statusText, data } = await otpCode.generate(form)

    loading.generating = false

    if (statusText == 'OK') {
        startCountdown(data.expires_at)
        headers[2].subTitle = t('otp-code-sent-to', [t(form.type), form[form.type]])
        state.value = 2
        errors.value = {}
    }
    else if (status === 422)
        errors.value = data.errors
    else if (status === 429)
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });
}

const verfiyCode = async () => {
    loading.verifing = true

    const { status, statusText, data } = await otpCode.verify(form)

    loading.verifing = false

    if (statusText == 'OK') {
        for (const key in form)
            delete form[key];
        router.push('/');
    }
    else if (status === 400 || status === 429)
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });
}

const countdown = reactive({ label: t('recieve-code') })

function startCountdown(expiryTime) {
    countdown.active = true
    const expiryDate = new Date(expiryTime);
    countdown.id = setInterval(() => {
        const now = new Date();
        const timeDifference = expiryDate - now;

        const minutes = '0' + Math.floor(timeDifference / (1000 * 60));
        const seconds = '0' + Math.floor((timeDifference % (1000 * 60)) / 1000);

        countdown.label = t('remaining-recieve-code', [minutes.slice(-2), seconds.slice(-2)]);

        if (timeDifference <= 0) stopCountdown()
    }, 1000);
}

function stopCountdown() {
    clearInterval(countdown.id);
    countdown.label = t('recieve-code');
    countdown.active = false
}


watch(emailOrMobile, (v) => {
    delete errors.value.type
    delete form.email
    delete form.mobile
    delete form.type

    let type = ''
    if (v.startsWith('09')) type = 'mobile'
    else if (v.includes('@')) type = 'email'

    if (type) {
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

<style lang="scss">
.custom-otp-input {
    width: 48px;
    height: 48px;
    font-size: 24px;
    appearance: none;
    text-align: center;
    transition: all 0.2s;
    border-radius: 0;
    border: 1px solid var(--p-form-field-border-color);
    background: transparent;
    outline-offset: -2px;
    outline-color: transparent;
    border-right: 0 none;
    transition: outline-color 0.3s;
    color: var(--p-inputtext-color);
}

.custom-otp-input:focus {
    outline: 2px solid var(--p-focus-ring-color);
}

.custom-otp-input:first-child,
.custom-otp-input:nth-child(5) {
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
}

.custom-otp-input:nth-child(3),
.custom-otp-input:last-child {
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
    border-right-width: 1px;
    border-right-style: solid;
    border-color: var(--p-form-field-border-color);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
