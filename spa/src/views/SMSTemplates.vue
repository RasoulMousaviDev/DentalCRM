<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" removable-sort>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold">{{ $t('sms-templates') }}</span>
                    <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                    <hr class="grow !ml-2"></hr>
                    <Button icon="pi pi-plus" :label="$t('new-template')" severity="success" @click="create()" />
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
            <Column :field="({ model }) => $t(model)" :header="$t('model')" class="w-44" />
            <Column field="status" :header="$t('status')" class="w-44">
                <template #body="{ data: { status } }">
                    <Tag v-bind="status" />
                </template>
            </Column>
            <Column field="template" :header="$t('template')" />
            <Column field="active" :header="$t('active')" class="w-24">
                <template #body="{ data: { active } }">
                    <Tag v-if="active" severity="success" :value="$t('active')" />
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
import SMSTemplateForm from '@/components/SMSTemplateForm.vue';
import { useCallsStore } from '@/stores/calls';
import { usePatientsStore } from '@/stores/patients';
import { useSMSTemplatesStore } from '@/stores/sms-templates';
import { inject } from 'vue';

const { dialog, confirm, toast, t } = inject('service')

const store = useSMSTemplatesStore()

if (store.items.length === 0)
    store.index()

const create = async () => {
    dialog.open(SMSTemplateForm, {
        props: {
            header: t('createNewSMSTemplate'), modal: true
        },
    })
}

const edit = async (data) => {
    const template = Object.assign({}, data)
    template.status = template.status.id

    dialog.open(SMSTemplateForm, {
        props: { header: t('editSMSTemplate'), modal: true },
        data: { template }
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

const patients = usePatientsStore()

const calls = useCallsStore()

const getModel = ({ model_name, model_id }) => {
    const model = { patient: patients.statuses, call: calls.statuses }

    const status = model[model_name].items.find((item) => item.id == model_id);

    return [t(model_name), status?.value].join(' - ')
}
</script>

<style lang="scss"></style>