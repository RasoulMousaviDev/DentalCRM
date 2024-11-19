<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-8 [&_small]:-mb-6 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputText v-model="form.title" :invalid="errors.title" fluid />
                <label>{{ $t('title') }}</label>
            </FloatLabel>
            <small v-if="errors.title" v-text="errors.title[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between items-center pr-2">
            <label> {{ $t('status') }}</label>
            <ToggleButton v-model="form.status" class="w-20" :onLabel="$t('active')" :offLabel="$t('deactive')" />
        </div>
        <div class="flex gap-2 mt-۴">
            <Button :label="$t('back')" severity="secondary" class="ml-auto" @click="dialogRef.close()" />
            <Button v-if="service" icon="pi pi-trash" severity="danger" outlined @click="destroy()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useTreatmentServicesStore } from '@/stores/treatment-services';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast, confirm, t } = inject('service')

const dialogRef = inject('dialogRef')
const { service } = dialogRef.value.data || {}

const form = reactive({ title: '', status: true })
const errors = ref({})
const loading = ref(false)

const services = useTreatmentServicesStore()

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (service)
        result = await services.update(service.id, form)
    else
        result = await services.store(form)

    const { status, statusText, data } = result

    loading.value = false

    if (statusText === 'OK')
        dialogRef.value.close();
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}

const destroy = () => {
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
            service.loading = true

            const { statusText, data } = await services.destroy(service.id);

            service.loading = false

            if (statusText == 'OK') {
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
                dialogRef.value.close(true)
            } else
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });

        }
    });
}

watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})

onMounted(() => {
    if (service)
        Object.keys(form).forEach((key) => form[key] = service[key])
})
</script>

<style lang="scss" scoped></style>