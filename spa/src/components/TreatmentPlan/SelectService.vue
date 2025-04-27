<template>
    <div class="flex gap-2 max-w-80 min-w-72">
        <FloatLabel variant="on" class="grow">
            <Select v-model="service" :options="props.data.options" optionLabel="title" optionValue="id" fluid
                show-clear label-class="truncate !max-w-44 ml-auto" :readonly="readonly">
                <template #option="{ option: { title, cost } }">
                    <div class="w-full flex items-center justify-between text-sm">
                        <span>{{ title }}</span>
                        <span>{{ formatNumber(cost) }}</span>
                    </div>
                </template>
            </Select>
            <label>{{ $t(props.data.title) }}</label>
        </FloatLabel>
        <FloatLabel v-if="props.data.manually" variant="on">
            <InputNumber v-model="count" mode="decimal" showButtons :min="1"
                input-class="w-[4.5rem] print:w-8" class="rtl [&>span>button]:w-6 [&>span>button]:print:hidden"
                :readonly="!service || readonly" />
            <label>{{ $t('count') }}</label>
        </FloatLabel>
    </div>
</template>

<script setup>
import { formatNumber } from '@/utils/format-number';
import { onBeforeMount, ref, watchEffect } from 'vue';

const props = defineProps({ data: Object, readonly: Boolean })

const model = defineModel()

const service = ref()

const count = ref(1)

watchEffect(() => model.value = service.value ? [service.value, count.value].join(':') : undefined)

onBeforeMount(() => {
    if (model.value) {
        const [s, c] = model.value.split(':')
        service.value = +s
        count.value = c
    }
})
</script>

<style lang="scss" scoped></style>