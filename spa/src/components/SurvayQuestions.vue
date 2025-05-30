<template>
    <div class="p-4 shadow-inner rounded bg-surface-50/70">
        <DataTable :value="store.items" @rowReorder="reorder"
            class="[&_table_tr]:!bg-transparent [&_.p-datatable-header]:!bg-transparent [&_.p-datatable-footer]:!bg-transparent" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold">{{ $t('questions') }}                    </span>
                    <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                    <IconField>
                        <InputText v-model="store.filters.query" :placeholder="$t('search')" class="!bg-transparent" />
                        <InputIcon :class="`pi pi-${store.fetching ? 'spin pi-spinner' : 'search'}`" />
                    </IconField>
                    <hr class="grow !ml-2"></hr>
                    <Button icon="pi pi-plus" :label="$t('new-question')" outlined severity="success"
                        @click="create()" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <template #footer>
                <Paginator v-if="store.pagiantor.totalRecords" v-bind="store.pagiantor" class="[&>*]:!bg-transparent [&_.p-paginator-page-selected]:!bg-white" @page="store.paginate" />
            </template>
            <Column v-if="!store.filters.query" rowReorder headerStyle="width: 3rem" />
            <Column :header="$t('row')" class="w-20">
                <template #body="{ index }">
                    {{ index + 1 }}
                </template>
            </Column>
            <Column field="title" :header="$t('title')" />
            <Column field="status" :header="$t('status')" class="w-24">
                <template #body="{ data: { status } }">
                    <Tag v-if="status" severity="success" :value="$t('active')" />
                    <Tag v-else severity="danger" :value="$t('deactive')" />
                </template>
            </Column>
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
import { useSurvayQuetionsStore } from '@/stores/survay-questions';
import { defineAsyncComponent, inject, watch } from 'vue';

const props = defineProps(['id'])

const { dialog, confirm, toast, t } = inject('service')

const store = useSurvayQuetionsStore()
store.id = props.id

if (store.items.length === 0)
    store.index()

const SurvayQuestionForm = defineAsyncComponent(() => import('@/components/SurvayQuestionForm.vue'));

const create = async () => {
    dialog.open(SurvayQuestionForm, {
        props: {
            header: t('createNewQuestion'), modal: true
        },
    })
}

const edit = async (data) => {
    const question = Object.assign({}, data)

    dialog.open(SurvayQuestionForm, {
        props: { header: t('editQuestion'), modal: true },
        data: { question }
    })
}

const destroy = (question) => {
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
            question.loading = true

            const { statusText, data } = await store.destroy(question.id);

            question.loading = false

            if (statusText == 'OK')
                toast.add({  severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else
                toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 3000 });

        }
    });
}

const reorder = async ({ value }) => {
    const orders = value.map(({ id }, i) => ({ id, order: i + 1 }))

    await store.reorder(orders)

    toast.add({  summary: t('success'), detail: t('update-successfully'), severity: 'success', life: 3000 })
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

<style lang="scss">


</style>