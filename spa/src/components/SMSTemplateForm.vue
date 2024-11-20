<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex gap-4">
            <div class="flex flex-col gap-2 flex-1">
                <label class="has-[+*+small]:text-red-500"> {{ $t('model-name') }}</label>
                <Select v-model="form.model_name" :options="['patient', 'call']" :optionLabel="(item) => $t(item)" fluid
                     :placeholder="$t('choose')" class="has-[+small]:!border-red-500" />
                <small v-if="errors.model_name" v-text="errors.model_name[0]" class="text-red-500" />
            </div>
            <div class="flex flex-col gap-2 flex-1">
                <label class="has-[+*+small]:text-red-500"> {{ $t('model-status') }}</label>
                <Select v-model="form.model_id" :options="modelStatus.items" :loading="modelStatus.fetching"
                    :emptyMessage="$t('first-select-model-name')" optionValue="id" fluid 
                    :placeholder="$t('choose')" class="has-[+small]:!border-red-500">
                    <template #value="{ value }">
                        <Tag v-if="value" class="text-xs" v-bind="modelStatus.items.find(({ id }) => value == id)" />
                    </template>
                    <template #option="{ option }">
                        <Tag v-bind="option" class="text-xs" />
                    </template>
                </Select>
                <small v-if="errors.model_id" v-text="errors.model_id[0]" class="text-red-500" />
            </div>
        </div>
        <div class="flex flex-col gap-2 col-span-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('template') }}</label>
            <Textarea v-model="form.template" fluid rows="5" cols="30" class="has-[+small]:!border-red-500" />
            <small v-if="errors.template" v-text="errors.template[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between items-center mt-2">
            <label> {{ $t('status') }}</label>
            <ToggleButton v-model="form.status" class="w-20" :onLabel="$t('active')" :offLabel="$t('deactive')" />
        </div>
        <div class="flex justify-between gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useCallsStore } from '@/stores/calls';
import { usePatientsStore } from '@/stores/patients';
import { useSMSTemplatesStore } from '@/stores/sms-templates';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast, t } = inject('service')

const dialogRef = inject('dialogRef')
const { template } = dialogRef.value.data || {}

const form = reactive({ model_name: '', model_id: '', template: '', status: true })
const errors = ref({})
const loading = ref(false)

const patients = usePatientsStore()
const calls = useCallsStore()

const modelStatus = computed(() => {
    if (form.model_name == 'patient')
        return patients.statuses;
    else if (form.model_name == 'call')
        return calls.statuses
    else return { items: [] }
})

const smsTempaltes = useSMSTemplatesStore()

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
        toast.add({  severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

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