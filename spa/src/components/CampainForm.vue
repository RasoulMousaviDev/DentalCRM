<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500">{{ $t('title') }}</label>
            <InputText v-model="form.title" class="w-full has-[+small]:!border-red-500" />
            <small v-if="errors.title" v-text="errors.title[0]" class="text-red-500" />
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col gap-2">
                <label class="has-[+*+small]:text-red-500">{{ $t('start-date') }}</label>
                <DatePicker v-model="start_date" class="w-full [&>input]:has-[+small]:!border-red-500"
                    :placeholder="$t('choose')" :inputClass="{ 'ltr': form.start_date }"
                    panelClass="ltr -translate-x-10" dateFormat="yy/mm/dd" />
                <small v-if="errors.start_date" v-text="errors.start_date[0]" class="text-red-500" />
            </div>
            <div class="flex flex-col gap-2">
                <label class="has-[+*+small]:text-red-500">{{ $t('end-date') }}</label>
                <DatePicker v-model="end_date" class="w-full [&>input]:has-[+small]:!border-red-500"
                    :placeholder="$t('choose')" :inputClass="{ 'ltr': form.end_date }" panelClass="ltr -translate-x-10"
                    dateFormat="yy/mm/dd" />
                <small v-if="errors.end_date" v-text="errors.end_date[0]" class="text-red-500" />
            </div>
        </div>
        <div class="flex flex-col gap-2 col-span-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('desc') }}</label>
            <Textarea v-model="form.desc" fluid rows="5" cols="30" class="has-[+small]:!border-red-500" />
            <small v-if="errors.desc" v-text="errors.desc[0]" class="text-red-500" />
        </div>

        <div class="flex flex-col gap-2">
            <label>{{ $t('budget') }}</label>
            <InputGroup class="ltr">
                <InputGroupAddon>{{ $t('toman') }}</InputGroupAddon>
                <InputNumber v-model="form.budget" class="w-full" @input="delete errors.budget" />
            </InputGroup>
        </div>
        <div class="flex justify-between gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useCampainsStore } from '@/stores/campains';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast } = inject('service')

const dialogRef = inject('dialogRef')
const { campain } = dialogRef.value.data || {}


const date = (key) => ({
    get: () => {
        if (form[key]) {
            return new Date(...form[key].split('/'));
        }
        return null
    },
    set: (v) => form[key] = [
        v.getFullYear(),
        ('0' + (v.getMonth() + 1)).slice(-2),
        ('0' + v.getDate()).slice(-2)
    ].join('/')
})


const form = reactive({ title: '', desc: '', start_date: '', end_date: '', budget: null })
const start_date = computed(date("start_date"))
const end_date = computed(date("end_date"))
const errors = ref({})
const loading = ref(false)

const store = useCampainsStore()

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (campain)
        result = await store.update(campain.id, form)
    else
        result = await store.store(form)

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
    if (campain)
        Object.keys(form).forEach((key) => form[key] = campain[key])
})
</script>

<style lang="scss" scoped></style>