<template>
    <div class="flex">
        <div class="min-w-96 flex flex-col gap-4">
            <Listbox v-model="selectedSubCategory" :options="subCategories.items" class="w-full md:w-96"
                scrollHeight="70vh" pt:option="justify-between border-[auto]" pt:list="!divide-y" checkmark>
                <template #option="{ option }">
                    <span class="ml-auto">{{ option.title }}</span>
                    <Tag v-if="option.status" severity="success" :value="$t('active')" />
                    <Tag v-else severity="danger" :value="$t('deactive')" />
                </template>
                <template #empty>
                    <p class="text-center text-gray-500">
                        {{ subCategories.fetching ? $t('loading') : $t('sub-category-empty') }}
                    </p>
                </template>
            </Listbox>
            <div class="flex gap-4">
                <Button :label="$t('back')" severity="secondary" class="shrink-0" @click="dialogRef.close()" />
                <Button :label="$t('new-sub-category')" icon="pi pi-plus" severity="success" class="w-full"
                    @click="createSubCategory()" />
            </div>

        </div>
        <div class="flex max-w-0 transition-[max-width] duration-300 overflow-hidden"
            :class="{ '!max-w-[100vw] mr-6': selectedSubCategory }">
            <Transition name="fade" mode="out-in">
                <DataTable v-if="selectedSubCategory" :key="selectedSubCategory.id" :value="options.items"
                    class="shrink-0" @rowReorder="reorder">
                    <template #header>
                        <div class="flex gap-1 items-center">
                            <span class="text-2xl font-bold">
                                {{ selectedSubCategory.title }}
                            </span>
                            <Button icon="pi pi-pencil" rounded text severity="secondary" @click="editSubCategory()" />
                            <Button icon="pi pi-plus" :label="$t('new-option')" severity="success" class="mr-auto"
                                @click="create()" />
                        </div>
                    </template>
                    <template #empty>
                        <p class="text-center text-sm opacity-60">
                            {{ options.fetching ? $t('loading') : $t('not-found') }}
                        </p>
                    </template>
                    <Column :header="$t('row')" class="w-20">
                        <template #body="{ index }">
                            {{ index + 1 }}
                        </template>
                    </Column>
                    <Column field="title" :header="$t('title')" class="w-80" />
                    <Column :field="({ cost }) => [new Intl.NumberFormat().format(cost), $t('toman')].join(' ')"
                        :header="$t('cost')" class="w-44" />
                    <Column field="status" :header="$t('status')" class="w-24">
                        <template #body="{ data: { status } }">
                            <Tag v-if="status" severity="success" :value="$t('active')" />
                            <Tag v-else severity="danger" :value="$t('deactive')" />
                        </template>
                    </Column>
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
            </Transition>
        </div>
    </div>
</template>

<script setup>
import { defineAsyncComponent, inject, ref, watch } from 'vue';
import { useTreatmentSubCategoriesStore } from '@/stores/treatment-sub-categories';
import { useTreatmentSubCategoryOptionsStore } from '@/stores/treatment-sub-category-options';

const { dialog, confirm, toast, t } = inject('service')

const dialogRef = inject('dialogRef')

const { treatment } = dialogRef.value.data || {}

const subCategories = useTreatmentSubCategoriesStore()

const options = useTreatmentSubCategoryOptionsStore()

subCategories.treatment = treatment.id

subCategories.index()

options.treatment = treatment.id

const TreatmentSubCategoryForm = defineAsyncComponent(() => import('@/components/TreatmentSubCategoryForm.vue'));

const TreatmentSubCategoryOptionForm = defineAsyncComponent(() => import('@/components/TreatmentSubCategoryOptionForm.vue'));

const selectedSubCategory = ref();

const createSubCategory = async () => {
    dialog.open(TreatmentSubCategoryForm, {
        props: {
            header: t('createNewSubCategory'), modal: true
        },
    })
}

const editSubCategory = async () => {
    const subCategory = Object.assign({}, selectedSubCategory.value)

    dialog.open(TreatmentSubCategoryForm, {
        props: { header: t('editSubCategory'), modal: true },
        data: { subCategory },
        onClose: (opt) => {
            if (opt.data) selectedSubCategory.value = null
        }
    })
}

const create = async () => {
    dialog.open(TreatmentSubCategoryOptionForm, {
        props: {
            header: t('createNewOption'), modal: true
        },
    })
}

const edit = async (data) => {
    const option = Object.assign({}, data)

    dialog.open(TreatmentSubCategoryOptionForm, {
        props: { header: t('editOption'), modal: true },
        data: { option }
    })
}

const destroy = (option) => {
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
            option.loading = true

            const { statusText, data } = await options.destroy(option.id);

            option.loading = false

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });

        }
    });
}

watch(selectedSubCategory, (v) => {
    if (v) {
        options.subCategory = v.id
        options.index()
    }
})
</script>

<style lang="scss" scoped></style>