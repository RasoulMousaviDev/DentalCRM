<template>
    <AuthLayout v-bind="header">
        <Transition name="fade" mode="out-in" @beforeEnter="header = headers[state]">
            <div v-if="state === 0" class="flex flex-col w-full md:w-[30rem]">
                <label for="email" class="text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">
                    {{ $t('phone-or-email') }}
                </label>
                <InputText id="email" type="text" dir="ltr" class="w-full mb-8" v-model="email" />

                <Button :label="$t('confirmation')" class="w-full" @click="state++" />
            </div>
            <div v-else-if="state === 1" class="flex flex-col items-center w-full md:w-[30rem]">
                <InputOtp v-model="value" :length="6" style="gap: 0" dir="ltr" class="">
                    <template #default="{ attrs, events, index }">
                        <input type="text" v-bind="attrs" v-on="events" class="custom-otp-input" />
                        <div v-if="index === 3" class="px-5">
                            <i class="pi pi-minus" />
                        </div>
                    </template>
                </InputOtp>
                <div class="flex justify-between mt-12 self-stretch">
                    <Button :label="$t('resend-code')" link class="!p-0" />
                    <Button :label="$t('confirmation')" @click="state++" />
                </div>
            </div>
            <div v-else-if="state === 2" class="flex flex-col w-full md:w-[30rem]">
                <label for="password" class="text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">
                    {{ $t('new-password') }}
                </label>
                <Password id="password" v-model="password" :weakLabel="$t('weak')" :mediumLabel="$t('medium')"
                    :strongLabel="$t('strong')" :promptLabel="$t('pick-a-password')" dir="ltr" toggleMask
                    class="w-full mb-8" fluid>
                    <template #footer>
                        <Divider />
                        <ul class="pr-2 mr-2 my-0 leading-normal list-disc text-sm">
                            <li v-for="i in 4">
                                {{ $t('password-rules.' + (i - 1)) }}
                            </li>
                        </ul>
                    </template>
                </Password>
                <label for="password" class="text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">
                    {{ $t('new-password-repeat') }}
                </label>
                <Password id="password" v-model="password" dir="ltr" toggleMask class="mb-10" fluid :feedback="false" />

                <Button :label="$t('save')" class="w-full" @click="state = 0" />
            </div>
        </Transition>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layout/Auth.vue';
import { inject, reactive, ref } from 'vue';

const { t } = inject('service')

const state = ref(0)
const headers = reactive([
    { title: t('change-password'), subTitle: t('change-password-desc') },
    { title: t('enter-otp-code'), subTitle: t('otp-code-sent-to', [t('phone'), '09102836220']) },
    { title: t('change-password'), subTitle: t('password-rule-8-charcter') },
])
const header = ref(headers[0])

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
