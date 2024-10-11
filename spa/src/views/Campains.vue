<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" removable-sort>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('campains') }}
                    </span>
                    <IconField>
                        <InputText v-model="store.filters.query" :placeholder="$t('search')" />
                        <InputIcon :class="`pi pi-${store.fetching ? 'spin pi-spinner' : 'search'}`" />
                    </IconField>
                    <Button icon="pi pi-plus" :label="$t('new-campain')" severity="success" @click="create()" />
                    <Button icon="pi pi-file-import" :label="$t('data-entry')" severity="info" @click="dataEntry()" />
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
            <Column field="title" :header="$t('title')" />
            <Column field="desc" :header="$t('desc')" />
            <Column field="start_date" :header="$t('start-date')" />
            <Column field="end_date" :header="$t('end-date')" />
            <Column
                :field="({ budget }) => budget ? [new Intl.NumberFormat().format(budget), $t('toman')].join(' ') : '---'"
                :header="$t('budget')" />
            <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
            <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />

            <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button icon="pi pi-pencil" rounded text severity="secondary" @click="edit(data)" />
                        <Button icon="pi pi-trash" rounded text severity="danger" :loading="data.loading"
                            @click="destroy(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import { useCampainsStore } from '@/stores/campains';
import { defineAsyncComponent, inject, watch } from 'vue';

const { dialog, confirm, toast, t } = inject('service')

const store = useCampainsStore()

if (store.items.length === 0)
    store.index()

const CampainForm = defineAsyncComponent(() => import('@/components/CampainForm.vue'));

const DataEntryForm = defineAsyncComponent(() => import('@/components/DataEntryForm.vue'));

const create = async () => {
    dialog.open(CampainForm, {
        props: {
            header: t('createNewCampain'), modal: true
        },
    })
}

const dataEntry = () => {
    dialog.open(DataEntryForm, {
        props: {
            header: t('data-entry'), modal: true
        },
    })
}

const edit = async (data) => {
    const campain = Object.assign({}, data)

    dialog.open(CampainForm, {
        props: { header: t('editCampain'), modal: true },
        data: { campain }
    })
}

const destroy = (campain) => {
    confirm.require({
        message: t('delete-confirm-question'),
        header: t('danger-zone'),
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: t('cancel'),
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: t('delete'),
            icon: 'pi pi-trash',
            severity: 'danger',
        },
        accept: async () => {
            campain.loading = true

            const { statusText, data } = await store.destroy(campain.id);

            campain.loading = false

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });

        }
    });
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

<style lang="scss"></style>