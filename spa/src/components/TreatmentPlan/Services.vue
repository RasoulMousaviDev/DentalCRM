<template>
    <div class="card flex flex-col gap-4 border border-transparent" :class="{ '!border-red-500': errors }">
        <div class="text-lg font-bold flex gap-4">
            <span>{{ $t("required-services") }} </span>
            <hr class="grow" />
        </div>
        <ul class="flex flex-wrap gap-6">
            <li v-for="(service) in services" :key="service.id">
                <SelectService v-model="model[service.id]" :data="service" />
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import SelectService from './SelectService.vue'
import { useTreatmentsStore } from '@/stores/treatments';

const props = defineProps({ treatment: Number, readonly: Boolean, errors: String })

const model = defineModel()

const treatments = useTreatmentsStore();

const services = computed(() => treatments.items.find(({ id }) => props.treatment == id)?.services || [])
</script>

<style lang="scss" scoped></style>