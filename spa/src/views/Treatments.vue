<template>
    <div class="card">
        <DataTable :value="store.items" @rowReorder="reorder" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('treatments') }}
                    </span>
                    <Button icon="pi pi-plus" :label="$t('new-treatment')" severity="success" @click="create()" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <Column rowReorder headerStyle="width: 3rem" />
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
            <Column :header="$t('actions')" headerClass="[&>div]:justify-center w-20">
                <template #body="{ data }">
                    <div class="flex justify-end">
                        <Button icon="pi pi-sitemap !text-xl" rounded text severity="secondary"
                            @click="showServicess(data)" />
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
import TreatmentForm from '@/components/TreatmentForm.vue';
import TreatmentServices from '@/components/TreatmentServices.vue';
import { useTreatmentsStore } from '@/stores/treatments';
import { inject } from 'vue';

const { dialog, confirm, toast, t } = inject('service')

const store = useTreatmentsStore()

if (store.items.length === 0)
    store.index()

const create = async () => {
    dialog.open(TreatmentForm, {
        props: {
            header: t('createNewTreatment'), modal: true
        },
    })
}

const edit = async (data) => {
    const treatment = Object.assign({}, data)

    dialog.open(TreatmentForm, {
        props: { header: t('editTreatment'), modal: true },
        data: { treatment }
    })
}

const destroy = (template) => {
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
            template.loading = true

            const { statusText, data } = await store.destroy(template.id);

            template.loading = false

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });

        }
    });
}

const showServicess = (treatment) => {
    dialog.open(TreatmentServices, {
        props: { header: t('sub-categires-of', treatment), modal: true },
        data: { treatment }
    })
}

const reorder = async ({ value }) => {
    const orders = value.map(({ id }, i) => ({ id, order: i + 1 }))

    await store.reorder(orders)

    toast.add({ summary: t('success'), detail: t('update-successfully'), severity: 'success', life: 3000 })
}
</script>

<style lang="scss"></style>