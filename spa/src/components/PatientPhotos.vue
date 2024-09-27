<template>
    <form class="flex gap-8">
        <Galleria v-model:activeIndex="activeIndex" :value="store.items" :numVisible="5" containerClass="ltr w-[47rem]"
            thumbnailsPosition="right">
            <template #item="{ item: { image } }">
                <img :src="image" class="w-full block" />
                <div class="!absolute flex gap-2 top-3 left-3">
                    <Button icon="pi pi-ellipsis-v" iconClass="text-white" class="!bg-black/20" rounded text
                        size="small" :loading="loading" @click="toggle" />
                    <Button icon="pi pi-window-maximize" iconClass="text-white" class="!bg-black/20" rounded text
                        size="small" @click="fullScreen = true" />
                </div>
            </template>
            <template #thumbnail="{ item: { image } }">
                <img :src="image" class="!w-20 h-20 block" />
            </template>
            <template #caption="{ item: { title, desc } }">
                <div class="rtl flex flex-col gap-2 w-full">
                    <div class="text-xl font-bold">{{ title }}</div>
                    <p class="text-white">{{ desc }}</p>
                </div>
            </template>
        </Galleria>
        <Menu ref="menu" class="-translate-x-28 min-w-36" :model="items" :popup="true" />
        <Galleria v-model:activeIndex="activeIndex" v-model:visible="fullScreen" :value="store.items" :numVisible="9"
            containerStyle="max-width: 70%" maskClass="!bg-black/80" circular fullScreen showItemNavigators
            :showThumbnails="false">
            <template #item="{ item: { image } }">
                <img :src="image" class="w-full block" />
            </template>
        </Galleria>
        <PatientPhotoForm ref="form" />
    </form>
</template>

<script setup>
import { usePhotosStore } from "@/stores/photo";
import PatientPhotoForm from "./PatientPhotoForm.vue";
import { inject, ref } from "vue";

const { confirm, route, t } = inject('service')

const { id } = route.params

const store = usePhotosStore()
store.index({ patient: id })

const fullScreen = ref(false);
const activeIndex = ref(0);

const form = ref()
const loading = ref(false)
const menu = ref();
const items = ref([
    {
        label: t('edit'),
        icon: 'pi pi-pencil',
        command: () => {
            form.value.edit(store.items[activeIndex.value])
        }
    },
    {
        label: t('delete'),
        icon: 'pi pi-trash',
        class: '[&_span]:text-red-500',
        command: () => {
            destroy(store.items[activeIndex.value].id)
        }
    }
]);


const toggle = (event) => {
    menu.value.toggle(event);
};

const destroy = (id) => {
    confirm.require({
        message: t('delete-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('delete'),
            icon: 'pi pi-trash',
            severity: 'danger',
        },
        accept: async () => {
            loading.value = true

            const { statusText, data } = await store.destroy(id);

            loading.value = false

            if (statusText == 'OK')
                toast.add({  severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else
                toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 3000 });

        }
    });
}
</script>
