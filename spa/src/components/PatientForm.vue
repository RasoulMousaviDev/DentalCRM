<template>
    <form @submit.prevent="handleSubmit()"
        class="grid grid-cols-3 gap-x-4 gap-y-8 [&_small]:-mb-6 w-full md:w-[45rem] pt-2">
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputText v-model="form.firstname" :invalid="errors.firstname" fluid />
                <label>{{ $t('firstname') }}</label>
            </FloatLabel>
            <small v-if="errors.firstname" v-text="errors.firstname[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputText v-model="form.lastname" :invalid="errors.lastname" fluid />
                <label>{{ $t('lastname') }}</label>
            </FloatLabel>
            <small v-if="errors.lastname" v-text="errors.lastname[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.gender" :options="genders.items" :optionLabel="(opt) => $t(opt)" fluid
                    :invalid="errors.gender" />
                <label>{{ $t('gender') }}</label>
            </FloatLabel>
            <small v-if="errors.gender" v-text="errors.gender[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1 col-span-2 relative">
            <span class="text-xs self-end -mt-[18px]">
                {{ $t('enter-number-then-press-enter') }}
            </span>
            <FloatLabel variant="on">
                <AutoComplete v-model="form.mobiles" multiple fluid :typeahead="false" v-keyfilter.int
                    class="ltr w-full [&>ul]:[&_li:last-child]:w-24" :invalid="errors.mobiles" @change="mobileValidate"
                    @keypress.enter.prevent="" />
                <label>{{ $t('mobile') }}</label>
            </FloatLabel>
            <small v-if="errors.mobiles" v-text="errors.mobiles[0]" class="text-red-500" />
            <template v-for="(m, i ) in form.mobiles">
                <small v-if="errors['mobiles.' + i]" v-text="errors['mobiles.' + i][0].replace('mobiles.' + i, m)"
                    class="text-red-500 [&:not(:first-of-type)]:hidden" />
            </template>
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.province" :options="provinces.items" optionLabel="title" optionValue="id" fluid
                    :invalid="errors.province" />
                <label> {{ $t('province') }}</label>
            </FloatLabel>
            <small v-if="errors.province" v-text="errors.province[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <InputText type="phone" v-model="form.telephone" :invalid="errors.telephone" fluid class="ltr"
                    v-keyfilter.int />
                <label> {{ $t('telephone') }}</label>
            </FloatLabel>
            <small v-if="errors.telephone" v-text="errors.telephone[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <DatePicker v-model="form.birthday" :invalid="errors.birthday" class="ltr" fluid
                    dateFormat="yy/mm/dd" />
                <label>{{ $t('birthday') }}</label>
            </FloatLabel>
            <small v-if="errors.birthday" v-text="errors.birthday[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.city" :options="form.province ? cities.items : []"
                    :emptyMessage="$t('first-select-province')" optionLabel="title" optionValue="id" fluid
                    :invalid="errors.city" />
                <label> {{ $t('city') }}</label>
            </FloatLabel>
            <small v-if="errors.city" v-text="errors.city[0]" class="text-red-500" />
        </div>
        <div v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)" class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.user"
                    :options="users.items.filter((user) => user.roles.some((role) => role.name == 'phone-consultant'))"
                    optionLabel="name" optionValue="id" fluid :loading="users.fetching" :invalid="errors.user" />
                <label> {{ $t('phone-consultant') }}</label>
            </FloatLabel>
            <small v-if="errors.user" v-text="errors.user[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1 col-span-2"
            :class="{ '!col-span-1': ['super-admin', 'admin'].includes(auth.user?.role?.name) }">
            <FloatLabel variant="on">
                <MultiSelect v-model="form.treatments" display="chip" :options="treatments.items"
                    :loading="treatments.fetching" optionLabel="title" optionValue="id" :showToggleAll="false" fluid
                    :invalid="errors.treatments" />
                <label> {{ $t('treatments') }}</label>
            </FloatLabel>
            <small v-if="errors.treatments" v-text="errors.treatments[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.lead_source" :options="leadSources.items" optionLabel="title" optionValue="id"
                    fluid :invalid="errors.lead_source" />
                <label> {{ $t('lead-source') }}</label>
            </FloatLabel>
            <small v-if="errors.lead_source" v-text="errors.lead_source[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1 col-span-2 row-span-2">
            <FloatLabel variant="on">
                <Textarea v-model="form.desc" fluid rows="5" cols="30" class="min-h-12" />
                <label> {{ $t('desc') }}</label>
            </FloatLabel>
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.insurance" :options="['has', 'not-has']" :optionLabel="(opt) => $t(opt)"
                    :optionValue="(opt) => opt == 'has'" fluid :invalid="errors.insurance" />
                <label>{{ $t('insurance') }}</label>
            </FloatLabel>
            <small v-if="errors.insurance" v-text="errors.insurance[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-1">
            <FloatLabel variant="on">
                <Select v-model="form.status" :options="statuses" optionValue="id" fluid :invalid="errors.status"
                    :disabled="store.statuses.filter(s => ['in-person-visit', 'online-visit'].includes(s.name)).map(s => s.id).includes(patient?.status)">
                    <template #value="{ value }">
                        <Tag v-if="value" class="text-xs" v-bind="store.statuses.find(({ id }) => value == id)" />
                    </template>
                    <template #option="{ option }">
                        <Tag v-bind="option" class="text-xs" />
                    </template>
                </Select>
                <label> {{ $t('status') }}</label>
            </FloatLabel>
            <small v-if="errors.status" v-text="errors.status[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between col-span-3 gap-2 mt-4">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close(false)" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
import { useCitiesStore } from '@/stores/cities';
import { useGendersStore } from '@/stores/genders';
import { useLeadSourcesStore } from '@/stores/lead-sources';
import { usePatientsStore } from '@/stores/patients';
import { useProvincesStore } from '@/stores/provinces';
import { useTreatmentsStore } from '@/stores/treatments';
import { useUsersStore } from '@/stores/users';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const auth = useAuthStore()

const users = useUsersStore()
users.pagiantor.rows = 1000
users.index()

const { toast } = inject('service')

const dialogRef = inject('dialogRef')

const { patient } = dialogRef.value.data || {}

const form = reactive({
    firstname: '',
    lastname: '',
    birthday: '',
    gender: '',
    telephone: '',
    mobiles: [],
    treatments: [],
    user: '',
    province: '',
    city: '',
    lead_source: '',
    insurance: '',
    status: '',
    desc: ''
})

const errors = ref({})
const loading = ref(false)

const store = usePatientsStore()
const statuses = computed(() => store.statuses.filter(s => {
    if (auth.user?.role?.name === 'reception')
        return ['in-person-visit'].includes(s.name)
    else return ['no-status', 'in-progress', 'not-needed'].includes(s.name)
}))
const provinces = useProvincesStore()
if (provinces.items.length < 1)
    provinces.index()
const cities = useCitiesStore()
const leadSources = useLeadSourcesStore()
if (leadSources.items.length < 1)
    leadSources.index()
const treatments = useTreatmentsStore()
const genders = useGendersStore()

const handleSubmit = async () => {
    loading.value = true

    let result;
    if (patient)
        result = await store.update(patient.id, form)
    else
        result = await store.store(form)

    const { status, statusText, data } = result

    loading.value = false

    if (statusText === 'OK')
        dialogRef.value.close(true);
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

watch(() => form.province, (v) => cities.index(v))

onMounted(() => {
    if (patient)
        Object.keys(form).forEach((key) => form[key] = patient[key])
    else {
        form.province = 8
        form.city = 141
    }

})
</script>

<style lang="scss" scoped></style>