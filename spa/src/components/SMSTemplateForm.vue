<template>
    <form @submit.prevent="handleSubmit()"
        class="grid grid-cols-2 gap-x-4 gap-y-8 [&_small]:-mb-6 w-full md:w-[30rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.model" :options="['patient', 'call']" :optionLabel="(item) => $t(item)"
                    :invalid="errors.model" fluid />
                <label> {{ $t('model-name') }}</label>
            </FloatLabel>
            <small v-if="errors.model" v-text="errors.model[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.status" :options="statuses" :emptyMessage="$t('first-select-model-name')"
                    optionValue="id" fluid :invalid="errors.status" class="has-[+small]:!border-red-500">
                    <template #value="{ value }">
                        <Tag class="text-xs" v-bind="statuses?.find(({ id }) => value == id)" />
                    </template>
                    <template #option="{ option }">
                        <Tag v-bind="option" class="text-xs" />
                    </template>
                </Select>
                <label> {{ $t('model-status') }}</label>
            </FloatLabel>
            <small v-if="errors.status" v-text="errors.status[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1 col-span-2">
            <FloatLabel variant="on">
                <Textarea v-model="form.template" fluid rows="5" cols="30" :invalid="errors.template" />
                <label> {{ $t('template') }}</label>
            </FloatLabel>
            <small v-if="errors.template" v-text="errors.template[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between items-center col-span-2 -mt-2 pr-2">
            <label> {{ $t('status') }}</label>
            <ToggleButton v-model="form.active" class="w-20" :onLabel="$t('active')" :offLabel="$t('deactive')" />
        </div>
        <div class="flex justify-between gap-2 col-span-2 mt-4">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useSMSTemplatesStore } from '@/stores/sms-templates';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast } = inject('service')

const dialogRef = inject('dialogRef')
const { template } = dialogRef.value.data || {}

const form = reactive({ model: null, status: null, template: null, active: true })
const errors = ref({})
const loading = ref(false)

const smsTempaltes = useSMSTemplatesStore()

const statuses = computed(() => smsTempaltes.statuses[form.model] || [])

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (template)
        result = await smsTempaltes.update(template.id, form)
    else
        result = await smsTempaltes.store(form)

    const { status, statusText, data } = result

    loading.value = false

    if (statusText === 'OK')
        dialogRef.value.close();
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}


watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
    })
})

onMounted(() => {
    if (template)
        Object.keys(form).forEach((key) => form[key] = template[key])
})
</script>

<style lang="scss" scoped></style>