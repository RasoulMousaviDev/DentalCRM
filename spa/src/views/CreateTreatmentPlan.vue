<template>
    <div class="max-w-full flex gap-8">
        <div class="grow max-w-[calc(100%_-_24rem)] flex flex-col gap-8">
            <Tabs v-model:value="currentTab" scrollable class="grow">
                <TabList class="ltr card !p-4 *:!shadow-none !mb-8" pt:tablist="gap-5 !border-0 px-4 py-2 rtl">
                    <Tab v-for="(item, i) in treatment.items" :key="i" :value="item.id" asChild v-slot="{}">
                        <Button :label="item.title" class="shrink-0"
                            :severity="currentTab == item.id ? 'primary' : 'secondary'" @click="currentTab = item.id"
                            :badge="tooths[item.id] ? tooths[item.id].length : null" />
                    </Tab>
                </TabList>
                <TabPanels class="card">
                    <TabPanel :value="null">
                        <div class="flex justify-center items-center h-60">
                            <Message severity="secondary" class="px-8">{{ $t('select-treatment') }}</Message>
                        </div>
                    </TabPanel>
                    <TabPanel v-for="(item, i) in treatment.items" :key="i" :value="item.id">
                        <SelectTooth v-model="tooths[`${item.id}`]" class="h-[30rem] mx-auto" />
                    </TabPanel>
                </TabPanels>
            </Tabs>

            <div class="card flex flex-wrap gap-6">
                <template v-for="(subCategory, i) in subCategories.items" :key="i">
                    <div class="flex flex-col gap-2 flex-1 max-w-80 min-w-72">
                        <label>{{ $t(subCategory.title) }}</label>
                        <Select v-model="form[subCategory.id]" :options="subCategory.options" optionLabel="title"
                            optionValue="id" fluid checkmark :placeholder="$t('choose')" show-clear />
                    </div>
                </template>
            </div>
        </div>

        <div class="w-96 shrink-0">
            <div class="card">

            </div>
        </div>
    </div>
</template>

<script setup>
import SelectTooth from '@/components/SelectTooth.vue';
import { useTreatmentSubCategoriesStore } from '@/stores/treatment-sub-categories';
import { useTreatmentsStore } from '@/stores/treatments';
import { reactive, ref, watch } from 'vue';

const form = reactive({})

const currentTab = ref(null)

const treatment = useTreatmentsStore()
treatment.index()

const tooths = reactive({})

const subCategories = useTreatmentSubCategoriesStore()
subCategories.items = []

watch(currentTab, (v) => {
    subCategories.treatment = v
    subCategories.index()
})
</script>

<style lang="scss" scoped></style>