<template>
    <div class="card">
        <DataTable :value="store.items" class="[&_td]:cursor-pointer whitespace-nowrap" tableStyle="min-width: 50rem"
            row-hover scrollable @row-click="showPatient">
            <template #header>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold">{{ $t('patients') }}</span>
                        <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                        <hr class="grow !ml-2">
                        </hr>
                        <Button icon="pi pi-file-excel" :label="$t('export')" severity="help" :loading="loading"
                            @click="exportExcel()" v-if="auth.user?.role?.name === 'super-admin'" />
                        <Button v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)"
                            icon="pi pi-arrow-right-arrow-left" :label="$t('transfer')" severity="info"
                            @click="transfer()" />
                        <Button icon="pi pi-plus" :label="$t('new-patient')" severity="success" @click="create()" />
                    </div>
                    <PatientFilters />
                </div>
            </template>
            <template #empty>
                <p class="text-center text-sm opacity-60">
                    {{ store.fetching ? $t('loading') : $t('not-found') }}
                </p>
            </template>
            <template #footer>
                <Paginator v-if="store.pagiantor.totalRecords" v-bind="store.pagiantor" @page="store.paginate" />
            </template>
            <Column :header="$t('row')" class="w-20">
                <template #body="{ index }">
                    {{ store.pagiantor.totalRecords - ((store.pagiantor.page - 1) * store.pagiantor.rows) - index }}
                </template>
            </Column>
            <Column :field="({ firstname, lastname }) => [firstname, lastname].join(' ')"
                :header="$t('name-and-family')" frozen />
            <Column v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)" field="user.name"
                :header="$t('phone-consultant')" />
            <Column v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)" field="treatment_plans.0.user.name"
                :header="$t('on-site-consultant')" />
            <Column :field="({ mobiles }) => mobiles.map(({ number }) => number).join(' | ')" :header="$t('mobile')"
                body-class="ltr !text-left" />
            <Column :field="({ telephone }) => telephone || '-'" :header="$t('telephone')"
                body-class="ltr !text-left" />
            <Column :field="({ treatments }) => treatments.map(({ title }) => title).join(' | ') || '-'"
                :header="$t('treatments')" class="max-w-52 truncate" />
            <Column field="status" :header="$t('patient-status')" class="whitespace-nowrap">
                <template #body="{ data: { status } }">
                    <Tag v-bind="status" />
                </template>
            </Column>
            <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
            <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />

            <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20" frozen
                align-frozen="right">
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button icon="pi pi-pencil" rounded text severity="secondary" @click="edit(data)" />
                        <Button v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)" icon="pi pi-trash"
                            rounded text severity="danger" :loading="data.loading" @click="destroy(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import * as XLSX from 'xlsx';
import PatientFilters from '@/components/PatientFilters.vue';
import PatientForm from '@/components/PatientForm.vue';
import { usePatientsStore } from '@/stores/patients';
import { useAuthStore } from '@/stores/auth';
import { inject, ref, watch } from 'vue';
import PatientTransfer from '@/components/PatientTransfer.vue';
import { useProvincesStore } from '@/stores/provinces';
import { useLeadSourcesStore } from '@/stores/lead-sources';
import { useCitiesStore } from '@/stores/cities';

const { dialog, confirm, toast, router, t } = inject('service')

const store = usePatientsStore()

const auth = useAuthStore()

const leadSources = useLeadSourcesStore()
const provinces = useProvincesStore()
const cities = useCitiesStore()

store.index()

const create = async () => {
    dialog.open(PatientForm, {
        props: {
            header: t('createNewPatient'), modal: true
        },
    })
}

const edit = async (data) => {
    const patient = Object.assign({}, data)
    patient.mobiles = patient.mobiles.map(({ number }) => number)
    patient.treatments = patient.treatments.map(({ id }) => id)
    patient.status = patient.status.id
    patient.user = patient.user.id

    dialog.open(PatientForm, {
        props: { header: t('editPatient'), modal: true },
        data: { patient }
    })
}

const destroy = (patient) => {
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
            patient.loading = true

            const { statusText, data } = await store.destroy(patient.id);

            if (statusText == 'OK')
                toast.add({ severity: 'success', summary: 'Success', detail: data.message, life: 3000 });
            else {
                patient.loading = false
                toast.add({ severity: 'error', summary: 'Error', detail: data.message, life: 3000 });
            }
        }
    });
}


const transfer = async () => {
    dialog.open(PatientTransfer, {
        props: {
            header: t('transferPatients'), modal: true
        },
    })
}

const loading = ref(false)

const exportExcel = async () => {
    loading.value = true

    const data = await store.export()

    if (data.length) {
        const mapData = data.map((d) => {
            return {
                [t('id')]: d.id,
                [t('firstname')]: d.firstname,
                [t('lastname')]: d.lastname,
                [t('gender')]: t(d.gender),
                [t('birthday')]: d.birthday,
                [t('telephone')]: d.telephone,
                [t('mobile')]: d.mobiles.map((mobile) => mobile.number).join(','),
                [t('province')]: provinces.items.find(({ id }) => id === d.province)?.title,
                [t('city')]: cities.items.find(({ id }) => id === d.city)?.title,
                [t('treatments')]: d.treatments.map(({ title }) => title).join(',') || '-',
                [t('insurance')]: d.insurance,
                [t('lead-source')]: leadSources.items.find(({ id }) => id === d.lead_source),
                [t('desc')]: d.desc,
                [t('phone-consultant')]: d.user.name,
                [t('on-site-consultant')]: d.treatment_plans[0]?.user.name,
                [t('status')]: d.status.value,
                [t('created_at')]: d.created_at,
                [t('updated_at')]: d.updated_at,
            }
        })
        const ws = XLSX.utils.json_to_sheet(mapData);

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

        const fileName = 'exported_data.xlsx';

        XLSX.writeFile(wb, fileName);

        toast.add({ severity: 'success', summary: 'Success', detail: t("export-success"), life: 5000 });
    } else {
        toast.add({ severity: 'error', summary: 'Error', detail: t('export-failed'), life: 5000 });
    }

    loading.value = false
}

const showPatient = ({ data: { id } }) => router.push({ name: 'Patient', params: { id } })

let timer;
watch(() => store.filters.query, (v) => {
    if (v != undefined) {
        clearTimeout(timer)
        timer = setTimeout(() => {
            if (v) store.filters = { query: v }
            else delete store.filters.query
            store.index()
        }, 300);
    }
})
</script>

<style lang="scss"></style>