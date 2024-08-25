<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem" removable-sort>
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('users') }}
                    </span>
                    <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" />
                    <Button icon="pi pi-plus" :label="$t('new-user')" severity="success" @click="create()" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <Column field="id" :header="$t('id')" />
            <Column field="name" :header="$t('name')" />
            <Column field="phone" :header="$t('phone')" />
            <Column field="email" :header="$t('email')" />
            <Column :field="({ roles }) => roles.map(({ title }) => title).join(' | ')" :header="$t('roles')" />
            <Column field="status" :header="$t('status')">
                <template #body="{ data: { status } }">
                    <Tag v-if="status" severity="success" :value="$t('active')" />
                    <Tag v-else severity="danger" :value="$t('deative')" />
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
import { useUsersStore } from '@/stores/users';
import { defineAsyncComponent, inject } from 'vue';

const { dialog, confirm, toast, t } = inject('service')

const store = useUsersStore()

if (store.items.length === 0)
    store.index()

const UserForm = defineAsyncComponent(() => import('@/components/UserFrom.vue'));

const create = async () => {
    dialog.open(UserForm, {
        props: {
            header: t('createNewUser'), modal: true
        },
    })
}

const edit = async (data) => {
    const user = Object.assign({}, data)
    user.roles = user.roles.map(({ id }) => id)

    dialog.open(UserForm, {
        props: { header: t('editUser'), modal: true },
        data: { user }
    })
}

const destroy = (data) => {
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
            data.loading = true

            const { statusText, data } = await store.destroy(data.id);

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                data.loading = false
                toast.add({ severity: 'info', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}
</script>

<style lang="scss"></style>