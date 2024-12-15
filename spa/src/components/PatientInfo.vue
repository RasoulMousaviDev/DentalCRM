<template>
    <div class="card flex flex-col">
        <div class="flex gap-2 items-center">
            <Button icon="pi pi-arrow-right" text rounded @click="$router.back()" />
            <span class="text-2xl font-bold ml-auto">
                {{ $t('patient-info') }}
            </span>
            <Button icon="pi pi-pencil" outlined severity="secondary" @click="edit(data)" />
            <Button icon="pi pi-trash" outlined severity="danger" :loading="loading" @click="destroy(data.id)" />
        </div>
        <ul v-if="data" class="grid grid-cols-5 border-t translate-y-4">
            <li v-for="(key, i) in keys" :key="key" class="flex items-center justify-between gap-2 px-4 py-3"
                :class="{ 'border-b': i < 13, 'col-span-2': i == 10 || i == 11, 'col-span-4 !justify-start': i == 13, 'border-l': ![4, 9, 12, 14].includes(i) }">
                <span class="opacity-70">{{ $t(key.replace('_', '-')) }}:</span>
                <div v-if="key == 'mobiles'" class="flex gap-2">
                    <Chip v-for="mobile in data.mobiles" :label="mobile.number" />
                </div>
                <div v-else-if="key == 'treatments'" class="flex gap-2 ml-auto">
                    {{ data.treatments.map(({ title }) => title).join(' | ') }}
                </div>
                <Tag v-else-if="key == 'status'" v-bind="data.status" />
                <span v-else-if="key == 'insurance'">
                    {{ $t(data.insurance ? 'has' : 'not-has') }}
                </span>
                <span v-else-if="key == 'birthday'">
                    {{ new Date(Date.parse(data.birthday)).toLocaleDateString('fa') }}
                </span>
                <span v-else class="font-medium" :class="{ 'ltr': (i + 1) % 5 === 0 }">
                    {{
                        key === 'gender' ? $t(data[key]) : (data[key]?.title || data[key])
                    }}
                </span>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { usePatientsStore } from '@/stores/patients';
import { defineAsyncComponent, inject, reactive, ref } from 'vue';

const props = defineProps(['data'])

const { dialog, confirm, toast, router, t } = inject('service')

const store = usePatientsStore()

const keys = reactive([
    'firstname', 'lastname', 'birthday', 'gender', 'created_at', 'province', 'city',
    'telephone', 'lead_source', 'updated_at', 'mobiles', 'treatments', 'insurance', 'desc', 'status'
])

const PatientForm = defineAsyncComponent(() => import('@/components/PatientForm.vue'));

const loading = ref(false)

const edit = async (data) => {
    const patient = Object.assign({}, data)
    patient.mobiles = patient.mobiles.map(({ number }) => number)
    patient.province = patient.province.id
    patient.city = patient.city.id
    patient.status = patient.status.id
    patient.lead_source = patient.lead_source.id

    dialog.open(PatientForm, {
        props: { header: t('editPatient'), modal: true },
        data: { patient }, onClose: () => store.show(patient.id)
    })
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
            loading.value = true

            const { statusText, data } = await store.destroy(id);

            if (statusText == 'OK') {
                router.replace({ name: 'Patients' })
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            }
            else {
                loading.value = false
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}
</script>

<style lang="scss" scoped></style>