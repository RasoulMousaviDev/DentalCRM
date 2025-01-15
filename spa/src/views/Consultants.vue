<template>
    <div class="card">
        <DataTable :value="store.items" scrollable>
            <template #header>
                <div class="flex items-center gap-2 pb-2">
                    <span class="text-2xl font-bold">{{ $t('consultants') }}</span>
                    <hr class="grow !ml-2">
                    </hr>
                    <div class="flex gap-3">
                        <InputGroup class="ltr !w-[19rem]">
                            <DatePicker v-model="date.from" inputClass="ltr" panelClass="ltr" dateFormat="yy/mm/dd" />
                            <InputGroupAddon>{{ $t('from') }}</InputGroupAddon>
                        </InputGroup>
                        <InputGroup class="ltr !w-[19rem]">
                            <DatePicker v-model="date.to" inputClass="ltr" panelClass="ltr" dateFormat="yy/mm/dd" />
                            <InputGroupAddon>{{ $t('to') }}</InputGroupAddon>
                        </InputGroup>
                        <Button :label="$t('search')" :loading="store.fetching" @click="store.index(date)" />
                    </div>
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <Column field="name" :header="$t('name')" class="whitespace-nowrap" frozen alignFrozen="right" />

            <Column v-for="(day, key) in store.days" :field="key" class="*:!justify-center w-24">
                <template #header>
                    <div class="flex flex-col gap-2 items-center whitespace-nowrap">
                        <span>{{ day }}</span>
                        <span>{{ key }}</span>
                    </div>
                </template>
                <template #body="{ data, }">
                    <div class="flex gap-2 items-center py-1">
                        <span class="py-1 rounded px-3 border-b-2 border-green-500 bg-[var(--surface-ground)]">{{
                            data[key].visited }}</span>
                        <span class="py-1 rounded px-3 border-b-2 border-blue-500 bg-[var(--surface-ground)]">{{
                            data[key].total }}</span>
                        <span class="py-1 rounded px-3 border-b-2 border-red-500 bg-[var(--surface-ground)]">{{
                            data[key].canceled }}</span>
                    </div>
                </template>
            </Column>

        </DataTable>
    </div>
</template>

<script setup>
import MyDate from '../utils/MyDate'
import { useConsultantsStore } from '@/stores/consultants';
import { reactive } from 'vue';

const today = new Date()
const day = today.getDay() + 1;
const oneDay = 24 * 60 * 60 * 1000;

const date = reactive({
    from:  new MyDate(today.getTime() - (day * oneDay)),
    to: new MyDate(today.getTime() + ((6 - day) * oneDay)),
})

const store = useConsultantsStore()
store.index(date)

</script>

<style lang="scss" scoped></style>
