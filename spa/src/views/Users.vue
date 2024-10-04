<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold ml-auto">
                        {{ $t('users') }}
                    </span>
                    <IconField>
                        <InputText v-model="store.filters.query" :placeholder="$t('search')" />
                        <InputIcon :class="`pi pi-${store.fetching ? 'spin pi-spinner' : 'search'}`" />
                    </IconField>
                    <Button icon="pi pi-filter" :label="$t('filter')" severity="secondary" @click="popover.show" />
                    <Button icon="pi pi-plus" :label="$t('new-user')" severity="success" @click="create()" />
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
            <Column field="id" :header="$t('id')" />
            <Column field="name" :header="$t('name')" />
            <Column field="mobile" :header="$t('mobile')" />
            <Column field="email" :header="$t('email')" />
            <Column :field="({ roles }) => roles.map(({ title }) => title).join(' | ')" :header="$t('roles')" />
            <Column field="status" :header="$t('status')">
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
import { useUsersStore } from '@/stores/users';
import { defineAsyncComponent, inject, ref, watch } from 'vue';

const { dialog, confirm, popover, toast, t } = inject('service')

popover.value.component = defineAsyncComponent(() => import('@/components/UserFilters.vue'));

const UserForm = defineAsyncComponent(() => import('@/components/UserForm.vue'));

const store = useUsersStore()

if (store.items.length === 0)
    store.index()

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

const destroy = (user) => {
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
            user.loading = true

            const { statusText, data } = await store.destroy(user.id);

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                user.loading = false
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
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