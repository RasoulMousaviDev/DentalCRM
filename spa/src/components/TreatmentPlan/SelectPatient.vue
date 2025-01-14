<template>
    <div class="card !p-6 flex flex-col gap-5">
        <div class="flex justify-between items-center border-b pb-4">
            <span class="text-lg font-bold">
                {{ $t("visit-type") }}
            </span>
            <SelectButton v-model="model.visit_type" :options="['online', 'in-person']"
                :optionLabel="(item) => $t(item)" class="ltr" :disabled="readonly" />
        </div>
        <FloatLabel variant="on">
            <Select v-model="model.id" :options="patients.items" option-value="id" :loading="patients.fetching"
                resetFilterOnHide autoFilterFocus filter :filterFields="[
                    ({ mobiles }) =>
                        mobiles.map((m) => m.number).join(' '),
                ]" :optionLabel="({ firstname, lastname }) =>
                        [firstname, lastname].join(' ')
                        " fluid :disabled="readonly"
                panel-class="[&_.p-iconfield]:ltr [&_input:not(.p-filled)]:!text-right"
                :filter-placeholder="$t('search-patient')" :invalid="errors" @filter="patients.search($event.value)">
                <template #empty>
                    <i></i>
                </template>
                <template #emptyfilter>
                    <span v-if="!patients.fetching">{{ $t('not-found') }}</span>
                    <span v-else>{{ $t('searching') }}</span>
                </template>
            </Select>
            <label> {{ $t("patient") }}</label>
        </FloatLabel>
        <small v-if="errors" class="text-red-500 -my-4">{{ errors[0] }}</small>
        <FloatLabel variant="on">
            <Textarea v-model="model.desc" fluid rows="5" cols="30" :readonly="readonly" />
            <label> {{ $t("desc") }}</label>
        </FloatLabel>
    </div>
</template>

<script setup>
import { usePatientsStore } from '@/stores/patients';

defineProps({ readonly: Boolean, errors: { type: String } })

const model = defineModel()

const patients = usePatientsStore();
patients.index()
</script>

<style lang="scss" scoped></style>