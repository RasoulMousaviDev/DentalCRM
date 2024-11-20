<template>
    <div class="card">
        <DataTable v-model:expandedRows="expandedRows" :value="store.items" tableStyle="min-width: 50rem"
            removable-sort>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold">{{ $t('survays') }}</span>
                    <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                    <IconField>
                        <InputText v-model="store.filters.query" :placeholder="$t('search')" />
                        <InputIcon :class="`pi pi-${store.fetching ? 'spin pi-spinner' : 'search'}`" />
                    </IconField>
                    <hr class="grow !ml-2"></hr>
                    <Button icon="pi pi-plus" :label="$t('new-survay')" severity="success" @click="create()" />
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
            <template #expansion="{ data: { id } }">
                <SurvayQuestions :id="id" />
            </template>
            <Column expander class="w-4 [&_button]:has-[[aria-expanded='false']]:rotate-180" />
            <Column :header="$t('row')" class="w-20">
                <template #body="{ index }">
                    {{ index + 1 }}
                </template>
            </Column>
            <Column field="title" :header="$t('title')" />
            <Column field="desc" :header="$t('desc')" />
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
import SurvayForm from '@/components/SurvayForm.vue';
import SurvayQuestions from '@/components/SurvayQuestions.vue';
import { useSurvaysStore } from '@/stores/survays';
import { inject, ref, watch } from 'vue';

const { dialog, confirm, toast, t } = inject('service')

const expandedRows = ref([])

const store = useSurvaysStore()

if (store.items.length === 0)
    store.index()

const create = async () => {
    dialog.open(SurvayForm, {
        props: {
            header: t('createNewSurvay'), modal: true
        },
    })
}

const edit = async (data) => {
    const survay = Object.assign({}, data)

    dialog.open(SurvayForm, {
        props: { header: t('editSurvay'), modal: true },
        data: { survay }
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
                toast.add({  severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else
                toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 3000 });

        }
    });
}

watch(() => expandedRows.value.length, () => expandedRows.value = expandedRows.value.slice(-1))

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