<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" striped-rows>
            <template #header>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold">{{ $t('calls') }}</span>
                        <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                        <hr class="grow !ml-2">
                        </hr>
                        <Button icon="pi pi-plus" :label="$t('new-call')" severity="success" class="w-32" @click="create()" />
                    </div>
                    <CallFilters />
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
import CallFilters from '@/components/CallFilters.vue';
import CallForm from '@/components/CallForm.vue';
import { useCallsStore } from '@/stores/calls';
import { inject, watch } from 'vue';

const { dialog, t } = inject('service')

const store = useCallsStore()
store.index()

const create = async () => {
    dialog.open(CallForm, {
        props: {
            header: t('createNewCall'), modal: true
        },
    })
}
</script>

<style lang="scss" scoped></style>