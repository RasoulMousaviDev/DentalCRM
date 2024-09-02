<template>
    <div class="flex gap-8 m-auto p-8 shadow-inner rounded-xl bg-surface-50 dark:bg-surface-800">
        <div
            class="rounded-3xl w-96 h-80 overflow-hidden border-2 border-dashed dark:border-surface-700 bg-white dark:bg-surface-950">
            <div v-if="file" class="h-full w-full relative group">
                <img :src="file.objectURL" class="w-full h-full object-cover">
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-black/30 transition-opacity">
                    <div class="flex justify-center items-center h-full">
                        <Button icon="pi pi-trash" :label="$t('delete-image')" severity="danger" @click="file = null" />
                    </div>
                </div>
            </div>
            <label v-else class="cursor-pointer flex flex-col justify-center items-center h-full gap-8">
                <i class="pi pi-image !text-7xl"></i>
                <span>{{ $t('click-here-choose-image') }}</span>
                <input type="file" hidden @input="chooseImage">
            </label>
        </div>

        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <label class=" has-[+*+small]:text-red-500">{{ $t('title') }}</label>
                <InputText v-model="form.title" class="w-full has-[+small]:!border-red-500" />
                <small v-if="errors.title" v-text="errors.title[0]" class="text-red-500" />
            </div>

            <div class="flex flex-col gap-2 col-span-2">
                <label class="has-[+*+small]:text-red-500"> {{ $t('desc') }}</label>
                <Textarea v-model="form.desc" fluid rows="5" cols="30" />
            </div>
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

const form = reactive({})
const errors = ref({})
const loading = ref(false)
const file = ref()

const chooseImage = (e) => {
    file.value = e.target.files[0]
    file.value.objectURL = URL.createObjectURL(file.value)
}

</script>

<style lang="scss" scoped></style>