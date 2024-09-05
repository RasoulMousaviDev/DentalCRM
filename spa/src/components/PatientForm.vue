<template>
    <form @submit.prevent="handleSubmit()" class="grid grid-cols-2 gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500">{{ $t('name') }}</label>
            <InputText v-model="form.name" class="w-full has-[+small]:!border-red-500" />
            <small v-if="errors.name" v-text="errors.name[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class=" has-[+*+small]:text-red-500">{{ $t('national_code') }}</label>
            <InputText v-model="form.national_code" class="ltr w-full has-[+small]:!border-red-500" />
            <small v-if="errors.national_code" v-text="errors.national_code[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500">{{ $t('birthday') }}</label>
            <DatePicker v-model="date" class="w-full [&>input]:has-[+small]:!border-red-500" :placeholder="$t('choose')"
                :inputClass="{ 'ltr': form.due_date }" panelClass="ltr -translate-x-10" dateFormat="yy/mm/dd" />
            <small v-if="errors.birthday" v-text="errors.birthday[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 grow">
            <label class="has-[+*+small]:text-red-500">{{ $t('gender') }}</label>
            <Select v-model="form.gender" :options="genders" :optionLabel="(opt) => $t(opt)" fluid checkmark
                :placeholder="$t('choose')" class="has-[+small]:!border-red-500" />
            <small v-if="errors.gender" v-text="errors.gender[0]" class="text-red-500" />
        </div>

        <div class="flex flex-col gap-2 col-span-2">
            <label class="has-[+*+small]:text-red-500">
                {{ $t('mobile') }}
            </label>
            <AutoComplete v-model="form.mobiles" multiple fluid :typeahead="false"
                class="ltr w-full [&>ul]:has-[+small]:!border-red-500 [&_li:last-child]:w-24" @change="mobileValidate"
                @keypress.enter.prevent="" />
            <small v-if="errors.mobiles" v-text="errors.mobiles[0]" class="text-red-500" />
            <template v-for="(m, i ) in form.mobiles">
                <small v-if="errors['mobiles.' + i]" v-text="errors['mobiles.' + i][0].replace('mobiles.' + i, m)"
                    class="text-red-500 [&:not(:first-of-type)]:hidden" />
            </template>
        </div>

        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('province') }}</label>
            <Select v-model="form.province" :options="provinces.items" :loading="provinces.fetching" optionLabel="title"
                optionValue="id" fluid checkmark :placeholder="$t('choose')" class="has-[+small]:!border-red-500" />
            <small v-if="errors.province" v-text="errors.province[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('city') }}</label>
            <Select v-model="form.city" :options="cities.items" :loading="cities.fetching"
                :emptyMessage="$t('first-select-province')" optionLabel="title" optionValue="id" fluid checkmark
                :placeholder="$t('choose')" class="has-[+small]:!border-red-500" />
            <small v-if="errors.city" v-text="errors.city[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('lead_source') }}</label>
            <Select v-model="form.lead_source" :options="leadSources.items" :loading="leadSources.fetching"
                optionLabel="title" optionValue="id" fluid checkmark :placeholder="$t('choose')"
                class="has-[+small]:!border-red-500" />
            <small v-if="errors.lead_source" v-text="errors.lead_source[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('status') }}</label>
            <Select v-model="form.status" :options="statuses.items" :loading="statuses.fetching" optionValue="id" fluid
                checkmark class="has-[+small]:!border-red-500">
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="statuses.items.find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
            <small v-if="errors.status" v-text="errors.status[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 col-span-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('desc') }}</label>
            <Textarea v-model="form.desc" fluid rows="5" cols="30" />
        </div>
        <div class="flex justify-between col-span-2 gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useCitiesStore } from '@/stores/cities';
import { useLeadSourcesStore } from '@/stores/lead-sources';
import { usePatientStatuesStore } from '@/stores/patient-statuses';
import { usePatientsStore } from '@/stores/patients';
import { useProvincesStore } from '@/stores/provinces';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const { toast, } = inject('service')

const dialogRef = inject('dialogRef')
const { patient } = dialogRef.value.data || {}

const form = reactive({
    name: '',
    national_code: '',
    birthday: '',
    gender: '',
    mobiles: [],
    province: '',
    city: '',
    lead_source: '',
    status: '',
    desc: ''
})

const date = computed({
    get: () => {
        if (form.birthday) {
            const d = form.birthday.split('/');
            return new Date(d[0], d[1] - 1, d[2]);
        }
        return null
    },
    set: (v) => form.birthday = [
        v.getFullYear(),
        ('0' + (v.getMonth() + 1)).slice(-2),
        ('0' + v.getDate()).slice(-2)
    ].join('/')
})

const errors = ref({})
const loading = ref(false)

const provinces = useProvincesStore()
const statuses = usePatientStatuesStore()
const leadSources = useLeadSourcesStore()

statuses.index()
provinces.index()
leadSources.index()

const genders = reactive(['male', 'female'])

const patients = usePatientsStore()

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (patient)
        result = await patients.update(patient.id, form)
    else
        result = await patients.store(form)

    const { status, statusText, data } = result

    loading.value = false

    if (statusText === 'OK')
        dialogRef.value.close();
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 5000 });

}

const mobileValidate = (e) => {
    const input = e.originalEvent.target
    input.style.color = null
    const mobile = e.value.pop()
    if (mobile.length === 11 && mobile.startsWith('09') && !form.mobiles.includes(mobile))
        form.mobiles.push(mobile)
    else
        setTimeout(() => {
            input.value = mobile
            input.style.color = 'red'
        }, 1);
}

watch(computed(() => Object.assign({}, form)), (value, old) => {
    Object.keys(form).forEach((key) => {
        if (value[key] != old[key]) delete errors.value[key]
        if (key == 'mobiles')
            for (let i = 0; i < Math.min(value.mobiles.length, old.mobiles.length); i++) {
                if (value.mobiles[i] != old.mobiles[i])
                    delete errors.value['mobiles.' + i]
            }
    })
})

const cities = useCitiesStore()
watch(() => form.province, (v) => { cities.index(v) })

onMounted(() => {
    if (patient)
        Object.keys(form).forEach((key) => form[key] = patient[key])
})
</script>

<style lang="scss" scoped></style>