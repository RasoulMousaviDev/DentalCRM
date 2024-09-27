<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" striped-rows>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('calls') }}
                    </span>
                    <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <Column :header="$t('row')">
                <template #body="{ index }">
                    {{ index + 1 }}
                </template>
            </Column>
            <Column field="patient.name" :header="$t('patient-name')" />
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
import { defineAsyncComponent, inject } from 'vue';

const PatientCallForm = defineAsyncComponent(() => import('@/components/PatientCallForm.vue'));

const { dialog, t } = inject('service')

const store = useCallsStore()
store.index({ })

const create = async () => {
    dialog.open(PatientCallForm, {
        props: {
            header: t('createNewCall'), modal: true
        },
    })
}
</script>

<style lang="scss" scoped></style>