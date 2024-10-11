<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" striped-rows>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('calls') }}
                    </span>
                    <IconField>
                        <InputText v-model="store.filters.query" :placeholder="$t('search')" />
                        <InputIcon :class="`pi pi-${store.fetching ? 'spin pi-spinner' : 'search'}`" />
                    </IconField>
                    <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" @click="popover.show" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <template #footer>
                <Paginator v-if="store.pagiantor.totalRecords" v-bind="store.pagiantor" @page="store.paginate" />
            </template>
            <Column :header="$t('row')">
                <template #body="{ index }">
                    {{ index + 1 }}
                </template>
            </Column>
            <Column :field="({ patient: { firstname, lastname } }) => [firstname, lastname].join(' ')"
                :header="$t('patient-name')" />
            <Column field="mobile" :header="$t('mobile')" />
            <Column field="desc" :header="$t('desc')" />
            <Column field="log" :header="$t('log')" />
            <Column field="status" :header="$t('status')">
                <template #body="{ data: { status } }">
                    <Tag v-bind="status" />
                </template>
            </Column>
            <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
        </DataTable>
    </div>
</template>

<script setup>
import { useCallsStore } from '@/stores/calls';
import { defineAsyncComponent, inject, watch } from 'vue';

const { popover } = inject('service')

popover.value.component = defineAsyncComponent(() => import('@/components/CallFilters.vue'));

const store = useCallsStore()
store.index({})

let timer;
watch(() => store.filters.query, (v) => {
    if (v != undefined) {
        clearTimeout(timer)
        timer = setTimeout(() => {
            if (v) store.filters = { query: v }
            else delete store.filters.query
            store.index()
        }, 300);
    }
})
</script>

<style lang="scss" scoped></style>