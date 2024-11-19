<template>
    <form @submit.prevent="handleSubmit()"
        class="flex items-center gap-8 m-auto p-8 shadow-inner rounded-xl bg-surface-50 dark:bg-surface-800">
        <div
            class="rounded-3xl w-96 h-80 overflow-hidden border-2 border-dashed dark:border-surface-700 bg-white dark:bg-surface-950 has-[small]:border-red-500 has-[small]:text-red-500">
            <div v-if="form.image" class="h-full w-full relative group">
                <img :src="form.image.objectURL || form.image" class="w-full h-full object-cover">
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-black/30 transition-opacity">
                    <div class="flex justify-center items-center h-full">
                        <Button icon="pi pi-trash" :label="$t('delete-image')" severity="danger"
                            @click="form.image = null" />
                    </div>
                </div>
            </div>
            <label v-else class="cursor-pointer flex flex-col justify-center items-center h-full gap-8">
                <i class="pi pi-image !text-7xl"></i>
                <span>{{ $t('click-here-choose-image') }}</span>
                <input type="file" hidden @input="chooseImage">
                <small v-if="errors.image" v-text="errors.image[0]" class="text-red-500" />
            </label>
        </div>

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-1">
                <FloatLabel variant="on">
                    <InputText v-model="form.title" fluid :invalid="errors.title" />
                    <label>{{ $t('title') }}</label>
                </FloatLabel>
                <small v-if="errors.title" v-text="errors.title[0]" class="text-red-500" />
            </div>
            <div class="flex flex-col gap-1">
                <FloatLabel variant="on">
                    <Textarea v-model="form.desc" fluid rows="5" cols="30" />
                    <label> {{ $t('desc') }}</label>
                </FloatLabel>
            </div>
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { usePhotosStore } from '@/stores/photo';
import { computed, inject, ref, watch } from 'vue';

const { route, t } = inject('service')

const { id } = route.params

const form = ref({ patient: id })
const errors = ref({})
const loading = ref(false)

const photos = usePhotosStore()

const chooseImage = (e) => {
    form.value.image = e.target.files[0]
    form.value.image.objectURL = URL.createObjectURL(form.value.image)
}

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (form.value.id)
        result = await photos.update(form.value.id, form.value)
    else
        result = await photos.store(form.value)

    const { status, statusText, data } = result

    loading.value = false

    if (statusText === 'OK')
        form.value = { patient: id }
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });
}


const edit = ({ id, image, title, desc }) => {
    Object.assign(form.value, { id, image, title, desc })
}

defineExpose({ edit })

watch(computed(() => Object.assign({}, form.value)), (value, old) => {
    Object.keys(form.value).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})
</script>

<style lang="scss" scoped></style>