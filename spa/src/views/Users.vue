<template>
    <div class="card">
        <DataTable :value="products" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold">
                        {{ $t('users') }}
                    </span>
                    <Button icon="pi pi-refresh" rounded text class="ml-auto" />
                    <Button icon="pi pi-plus" :label="$t('new-user')" severity="success" />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">{{ $t('not-found') }}</p>
            </template>
            <Column field="name" filterField="name" :header="$t('name')" sortable  :showFilterMatchModes="false" :showFilterOperator="false">
            </Column>
            <Column field="phone" :header="$t('phone')" />
            <Column field="email" :header="$t('email')" />
            <Column field="status" :header="$t('status')">
                <template #body>
                    <Tag severity="danger" value="غیرفعال"></Tag>
                </template>
            </Column>
            <Column field="created_at" :header="$t('created_at')" />
            <Column field="updated_at" :header="$t('updated_at')" />

            <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
                <template #body>
                    <div class="flex gap-2 justify-end">
                        <Button icon="pi pi-pencil" rounded text severity="secondary" />
                        <Button icon="pi pi-trash" rounded text severity="danger" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

const products = reactive([
    // {
    //     id: '1000',
    //     name: 'رسول موسوی',
    //     phone: '09102836220',
    //     email: 'rasoulmousavidev@gmail.com',
    //     created_at: '1403/05/02',
    //     updated_at: '1403/05/02',
    // }
])


</script>

<style lang="scss"></style>