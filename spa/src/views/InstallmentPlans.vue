<template>
    <div class="card">
        <DataTable :value="store.items" @rowReorder="reorder" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('installment-plans') }}
                    </span>
                    <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                        <hr class="grow !ml-2"></hr>
                    <Button icon="pi pi-plus" :label="$t('new-plan')" severity="success" @click="create()" />
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
            <Column field="months_count" :header="$t('months-count')" />
            <Column field="deposit_percent" :header="$t('deposit-percent')" />
            <Column field="interest_percent" :header="$t('interest-percent')" />
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
import InstallmentPlanForm from '@/components/InstallmentPlanForm.vue';
import { useInstallmentPlansStore } from '@/stores/installment-plans';
import { inject } from 'vue';

const { dialog, confirm, toast, t } = inject('service')

const store = useInstallmentPlansStore()
store.index()

const create = async () => {
    dialog.open(InstallmentPlanForm, {
        props: {
            header: t('createNewInstallmentPlan'), modal: true
        },
    })
}

const edit = async (data) => {
    const plan = Object.assign({}, data)

    dialog.open(InstallmentPlanForm, {
        props: { header: t('editInstallmentPlan'), modal: true },
        data: { plan }
    })
}

const destroy = (plan) => {
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
            plan.loading = true

            const { statusText, data } = await store.destroy(plan.id);

            plan.loading = false

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });

        }
    });
}

</script>

<style lang="scss"></style>