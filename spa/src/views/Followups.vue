<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" striped-rows>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('follow-ups') }}
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
            <Column :header="$t('row')" class="w-20">
                <template #body="{ index }">
                    {{ index + 1 }}
                </template>
            </Column>
            <Column :field="({ patient: { firstname, lastname } }) => [firstname, lastname].join(' ')"
                :header="$t('patient-name')" />
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
    </div>
</template>

<script setup>
import { useFollowupsStore } from '@/stores/followups';
import { defineAsyncComponent, inject, reactive, watch } from 'vue';

const PatientCallForm = defineAsyncComponent(() => import('@/components/CallForm.vue'));

const severities = reactive({ pending: "warn", done: "success", missed: "danger" })

const { dialog, popover, t } = inject('service')

popover.value.component = defineAsyncComponent(() => import('@/components/FollowupFilters.vue'));

const store = useFollowupsStore()
store.index({})

const done = async (id) => {
    dialog.open(PatientCallForm, {
        props: {
            header: t('createNewCall'), modal: true
        },
        data: id
    })
}

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