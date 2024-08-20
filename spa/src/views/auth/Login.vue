<template>
    <AuthLayout :title="$t('welcome')" :sub-title="$t('login-to-continue')">
        <div class="flex flex-col w-full md:w-[30rem]">
            <label for="email" class="text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">
                {{ $t('phone-or-email') }}
            </label>
            <InputText id="email" type="text" dir="ltr" class="w-full mb-8" v-model="email" />

            <label for="password" class="text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">
                {{ $t('password') }}
            </label>
            <Password id="password" v-model="password" dir="ltr" toggleMask class="mb-4" fluid :feedback="false">
            </Password>

            <Button :label="$t('forgot-password') + '؟'" class="!px-0 mt-2 mb-6 self-end" link as="router-link"
                :to="{ name: 'ForgotPassword' }" />

            <Button :label="$t('login')" class="w-full" />
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layout/Auth.vue';
</script>

<style lang="scss"></style>
