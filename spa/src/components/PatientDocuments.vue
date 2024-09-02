<template>
    <form class="flex gap-8">
        <Galleria v-model:activeIndex="activeIndex" :value="images" :numVisible="5" containerClass="ltr w-[47rem]"
            thumbnailsPosition="right">
            <template #item="slotProps">
                <img :src="slotProps.item.itemImageSrc" :alt="slotProps.item.alt" style="width: 100%; display: block" />
                <div class="!absolute flex gap-2 top-3 left-3">
                    <Button icon="pi pi-ellipsis-v" iconClass="text-white" class="!bg-black/20"
                        rounded text size="small" @click="toggle" />
                    <Button icon="pi pi-window-maximize" iconClass="text-white" class="!bg-black/20"
                        rounded text size="small" @click="fullScreen = true" />
                </div>
            </template>
            <template #thumbnail="slotProps">
                <img :src="slotProps.item.thumbnailImageSrc" :alt="slotProps.item.alt" style="display: block" />
            </template>
            <template #caption="slotProps">
                <div class="rtl flex flex-col gap-2">
                    <div class="text-xl font-bold">{{ slotProps.item.title }}</div>
                    <p class="text-white">{{ slotProps.item.alt }}</p>
                </div>
            </template>
        </Galleria>
        <Menu ref="menu" class="-translate-x-28 min-w-36" :model="items" :popup="true" />
        <Galleria v-model:activeIndex="activeIndex" v-model:visible="fullScreen" :value="images" :numVisible="9"
            containerStyle="max-width: 70%" maskClass="!bg-black/80" circular fullScreen showItemNavigators
            :showThumbnails="false">
            <template #item="slotProps">
                <img :src="slotProps.item.itemImageSrc" :alt="slotProps.item.alt" style="width: 100%; display: block" />
            </template>
        </Galleria>
        <PatientDocumentForm />
    </form>
</template>

<script setup>
import PatientDocumentForm from "./PatientDocumentForm.vue";
import { inject, ref } from "vue";

const { t } = inject('service')

const images = ref([
    {
        itemImageSrc: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1.jpg',
        thumbnailImageSrc: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1s.jpg',
        alt: 'Description for Image 1',
        title: 'Title 1'
    },
    {
        itemImageSrc: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1.jpg',
        thumbnailImageSrc: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1s.jpg',
        alt: 'Description for Image 1',
        title: 'Title 1'
    },
    {
        itemImageSrc: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1.jpg',
        thumbnailImageSrc: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1s.jpg',
        alt: 'Description for Image 1',
        title: 'Title 1'
    },
]);

const fullScreen = ref(false);
const activeIndex = ref(0);

const menu = ref();
const items = ref([

    {
        label: t('edit'),
        icon: 'pi pi-pencil'
    },
    {
        label: t('delete'),
        icon: 'pi pi-trash',
        class: '[&_span]:text-red-500'
    }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};
</script>
