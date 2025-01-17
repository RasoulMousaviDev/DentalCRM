<template>
    <div class="flex">
        <div class="min-w-96 flex flex-col gap-4">
            <Listbox v-model="selectedService" :options="services.items" class="w-full md:w-96" scrollHeight="70vh"
                pt:option="justify-between border-[auto]" pt:list="!divide-y" >
                <template #option="{ option }">
                    <span class="ml-auto">{{ option.title }}</span>
                    <Tag v-if="option.status" severity="success" :value="$t('active')" />
                    <Tag v-else severity="danger" :value="$t('deactive')" />
                </template>
                <template #empty>
                    <p class="text-center text-gray-500">
                        {{ services.fetching ? $t('loading') : $t('service-empty') }}
                    </p>
                </template>
            </Listbox>
            <div class="flex gap-4">
                <Button :label="$t('back')" severity="secondary" class="shrink-0" @click="dialogRef.close()" />
                <Button :label="$t('new-service')" icon="pi pi-plus" severity="success" class="w-full"
                    @click="createdService()" />
            </div>

        </div>
        <div class="flex max-w-0 transition-[max-width] duration-300 overflow-hidden"
            :class="{ '!max-w-[100vw] mr-6': selectedService }">
            <Transition name="fade" mode="out-in">
                <DataTable v-if="selectedService" :key="selectedService.id" :value="options.items" class="shrink-0"
                    @rowReorder="reorder">
                    <template #header>
                        <div class="flex gap-1 items-center">
                            <span class="text-2xl font-bold">
                                {{ selectedService.title }}
                            </span>
                            <Button icon="pi pi-pencil" rounded text severity="secondary" @click="editService()" />
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
import { inject, ref, watch } from 'vue';
import { useTreatmentServicesStore } from '@/stores/treatment-services';
import { useTreatmentServiceOptionsStore } from '@/stores/treatment-service-options';
import TreatmentServiceForm from '@/components/TreatmentServiceForm.vue';
import TreatmentServiceOptionForm from '@/components/TreatmentServiceOptionForm.vue';

const { dialog, confirm, toast, t } = inject('service')

const dialogRef = inject('dialogRef')

const { treatment } = dialogRef.value.data || {}

const services = useTreatmentServicesStore()

const options = useTreatmentServiceOptionsStore()

services.treatment = treatment.id

services.index()

options.treatment = treatment.id

const selectedService = ref();

const createdService = async () => {
    dialog.open(TreatmentServiceForm, {
        props: {
            header: t('createNewService'), modal: true
        },
    })
}

const editService = async () => {
    const service = Object.assign({}, selectedService.value)

    dialog.open(TreatmentServiceForm, {
        props: { header: t('editService'), modal: true },
        data: { service },
        onClose: (opt) => {
            if (opt.data) selectedService.value = null
            else selectedService.value = services.items.find(({ id }) => service.id == id)
        }
    })
}

const create = async () => {
    dialog.open(TreatmentServiceOptionForm, {
        props: {
            header: t('createNewOption'), modal: true
        },
    })
}

const edit = async (data) => {
    const option = Object.assign({}, data)

    dialog.open(TreatmentServiceOptionForm, {
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

watch(selectedService, (v) => {
    if (v) {
        options.items = []
        options.service = v.id
        options.index()
    }
})
</script>

<style lang="scss" scoped></style>