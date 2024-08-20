<template>
    <FloatingConfigurator />
    <div class="bg-surface-50 dark:bg-surface-950 flex min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center m-auto">
            <div class="rounded-[56px] p-[0.3rem]"
                style="background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20 rounded-[53px]">
                    <div class="text-center mb-8 transition-opacity duration-500 has-[+.fade-leave-to]:opacity-0">
                        <IconSikai />
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">
                            {{ title }}
                        </div>
                        <p class="text-muted-color font-medium">
                            {{ subTitle }}
                        </p>
                    </div>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps(['title', 'subTitle'])

import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import IconSikai from '@/components/icon/Sakai.vue'
</script>

<style lang="scss">

</style>
