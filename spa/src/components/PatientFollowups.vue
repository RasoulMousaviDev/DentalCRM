<template>
    <DataTable :value="store.items" tableStyle="min-width: 50rem" striped-rows>
        <template #header>
            <div class="flex items-center gap-2">
                <IconField class="ml-auto">
                    <InputText v-model="value1" :placeholder="$t('search')" />
                    <InputIcon class="pi pi-search" />
                </IconField>
                <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" />
            </div>
        </template>
        <template #empty>
            <p class="text-center text-sm opacity-60">
                {{ store.fetching ? $t('loading') : $t('not-found') }}
            </p>
        </template>
        <Column :header="$t('row')" class="w-20">
            <template #body="{ index }">
                {{ index + 1 }}
            </template>
        </Column>
        <Column field="desc" :header="$t('desc')" />
        <Column field="due_date" :header="$t('due_date')" bodyClass="ltr" class="w-44" />
        <Column field="status" :header="$t('status')" class="w-32">
            <template #body="{ data: { status } }">
                <Tag :value="$t(status)" :severity="severities[status]" />
            </template>
        </Column>
        <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
        <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />
        <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
            <template #body="{ data: { id, status } }">
                <div class="flex gap-2 pr-2">
                    <i v-if="status == 'done'" class="pi pi-check text-green-500 p-3"></i>
                    <Button v-else icon="pi pi-check" size="small" rounded severity="success" @click="done(id)" />
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { useFollowupsStore } from '@/stores/followups';
import { defineAsyncComponent, inject, reactive } from 'vue';

const PatientCallForm = defineAsyncComponent(() => import('@/components/CallForm.vue'));

const severities = reactive({ pending: "warn", done: "success", missed: "danger" })

const { dialog, route, t } = inject('service')

const { id } = route.params

const store = useFollowupsStore()
store.index({ patient: id })

const done = async (id) => {
    dialog.open(PatientCallForm, {
        props: {
            header: t('createNewCall'), modal: true
        },
        data: id
    })
}
</script>

<style lang="scss" scoped></style>