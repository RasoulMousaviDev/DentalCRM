<template>
    <DataTable :value="[]" tableStyle="min-width: 50rem" row-hover>
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
                <!-- {{ store.fetching ? $t('loading') : $t('not-found') }} -->
            </p>
        </template>
        <Column :field="({ index }) => index + 1" :header="$t('row')" />
        <Column field="desc" :header="$t('desc')" />
        <Column field="due_date" :header="$t('due_date')" />
        <Column field="status" :header="$t('status')">
            <template #body="{ data: { status } }">
                <Tag :value="$t(status)" :severity="severities[status]" />
            </template>
        </Column>
        <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
        <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />
        <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button icon="pi pi-check" rounded outlined severity="success" @click="done(data)" />
                    </div>
                </template>
            </Column>
    </DataTable>
</template>

<script setup>
import { reactive } from 'vue';


const severities = reactive({ pending: "warn", done: "success", missed: "danger" })
</script>

<style lang="scss" scoped></style>