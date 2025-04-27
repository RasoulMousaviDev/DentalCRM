<template>
    <div class="card !p-4 flex gap-2 items-center border border-transparent" :class="{ '!border-red-500': errors }">
        <Button icon="pi pi-chevron-right" text rounded @click="() => treatRef.scrollTo(0, 0)" />
        <div ref="treatRef" class="w-full overflow-x-auto [&::-webkit-scrollbar]:hidden scroll-smooth">
            <ul class="flex gap-4 p-2">
                <li v-for="({ id, title }, i) in treatments.items.filter((item) => item.status)" :key="i"
                    class="shrink-0 py-1.5">
                    <Button :label="title" :severity="model == id ? 'primary' : 'secondary'"
                        :badge="Object.entries(props.tooth[`${id}`] || {}).filter(([,v]) => v).length" badge-class="absolute -top-2 -right-2" badge-severity="danger"
                        @click="model = id" />
                </li>
            </ul>
        </div>
        <Button icon="pi pi-chevron-left" text rounded @click="() => treatRef.scrollTo(-1000, 0)" />
    </div>
</template>

<script setup>
import { useTreatmentsStore } from '@/stores/treatments';
import { ref } from 'vue';

const props = defineProps({ tooth: Object, errors: String })

const model = defineModel()

const treatRef = ref();

const treatments = useTreatmentsStore();

</script>

<style lang="scss" scoped></style>