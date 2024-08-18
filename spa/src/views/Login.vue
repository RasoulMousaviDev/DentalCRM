<template>
    <div class="m-auto p-2">
        <div class="flex flex-col gap-8 bg-white p-16 rounded-2xl">
            <div class="flex flex-col gap-2 w-96">
                <label>Username</label>
                <InputText v-model="value" invalid />
                <small id="username-help" class="error">
                    Enter your username to reset your password.
                </small>
            </div>
            <div class="flex flex-col gap-2 w-96">
                <label>Password</label>
                <Password
                    v-model="value"
                    :feedback="false"
                    toggle-mask
                    inputClass="w-full"
                />
                <small id="username-help">
                    <!-- Enter your username to reset your password. -->
                </small>
            </div>
            <Button label="Login" />
        </div>
    </div>
</template>

<script setup></script>

<style lang="scss"></style>
