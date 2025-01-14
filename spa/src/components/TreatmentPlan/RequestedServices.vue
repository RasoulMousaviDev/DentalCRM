<template>
    <div class="card !p-6 flex-col gap-6 printable">
        <div class="flex justify-between items-center border-b pb-4">
            <span class="text-lg font-bold">
                {{ $t("requested-services") }}
            </span>
        </div>
        <ul class="flex flex-col gap-4 pr-2 [&_li]:w-full divide-y">
            <li v-for="({ tooth, treatments }, i) in tooths" :key="i" class="mt-4 border-r">
                <div class="inline-flex flex-col gap-3 w-full">
                    <div class="flex gap-4 items-center">
                        <span class="w-20">{{ tooth.direction }}</span>
                        <Tag severity="secondary" :value="tooth.position" />
                    </div>
                    <ul class="flex flex-col gap-4 pr-2 grow">
                        <li v-for="({ treatment, services }, j) in treatments" :key="j" class="border-r">
                            <div class="inline-flex flex-col gap-3 w-full">
                                <span class="font-bold"> {{ treatment.title }}</span>
                                <ul class="flex flex-col gap-4 pr-2 grow">
                                    <li v-for="(service, k) in services" :key="k">
                                        <div class="inline-flex justify-between gap-2 w-full">
                                            <OverlayBadge :value="service.count" size="small" severity="danger">
                                                <span class="truncate pl-3">{{ service.title }}</span>
                                            </OverlayBadge>
                                            <span>{{ [formatNumber(service.cost), $t("toman")].join(" ") }}</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <div class="-m-6 mt-0 print:mt-auto rounded-b-md overflow-hidden">
            <div class="border border-[var(--surface-card)] bg-[var(--surface-ground)]">
                <div class="flex justify-between items-center font-bold  px-6 py-4 ">
                    <span>{{ $t("final-amount") }}</span>
                    <span>{{ [formatNumber(total), $t("toman")].join(" ") }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useTreatmentsStore } from '@/stores/treatments';
import { formatNumber } from '@/utils/format-number';
import { computed, inject, ref } from 'vue';

const props = defineProps({ tooths: Object })

const emits = defineEmits(['total-amount'])

const { t } = inject('service')

const total = ref(0)

const treatmentsStore = useTreatmentsStore();

const tooths = computed(() => {
    total.value = 0

    const items = [];

    Object.entries(props.tooths).forEach(([tooth, treatments]) => {
        let position;
        tooth = +tooth
        tooth < 8 && (position = 8 - tooth);
        tooth > 7 && tooth < 15 && (position = tooth - 7);
        tooth > 14 && tooth < 22 && (position = 22 - tooth);
        tooth > 21 && (position = tooth - 21);

        const direction = [t(tooth < 15 ? "top" : "bottom"), t(tooth < 8 || (tooth > 14 && tooth < 22) ? "left" : "right")].join(" ")

        const _tooth = { tooth: { direction, position }, treatments: [] }

        Object.entries(treatments).forEach(([treatment, services]) => {
            const { title, services: _services } = treatmentsStore.items.find(({ id }) => treatment == id)

            const _treatment = { treatment: { title }, services: [] }

            Object.entries(services).forEach(([service, value]) => {
                if (value) {
                    const [option, count] = value.split(':')

                    const { options: _options } = _services.find(({ id }) => service == id)

                    let { title, cost } = _options.find(({ id }) => option == id)

                    cost *= count

                    _treatment.services.push({ title, cost, count })

                    total.value += cost

                }
            })
            
            _tooth.treatments.push(_treatment)
        })
        
        items.push(_tooth)
    })
    emits('total-amount', total.value)
    return items;
})

</script>

<style lang="scss" scoped></style>