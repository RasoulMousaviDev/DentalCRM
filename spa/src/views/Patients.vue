<template>
    <div class="card">
        <DataTable :value="store.items" class="[&_td]:cursor-pointer truncate" tableStyle="min-width: 50rem"
            removable-sort row-hover @row-click="showPatient">
            <template #header>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold">{{ $t('patients') }}</span>
                        <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                        <hr class="grow !ml-2">
                        </hr>
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
            <Column field="id" :header="$t('id')" />
            <Column v-if="['super-admin', 'admin'].includes(auth.user?.role.name)" field="user.name" :header="$t('consultant')" />
            <Column :field="({ firstname, lastname }) => [firstname, lastname].join(' ')"
                :header="$t('name-and-family')" />
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

            <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button icon="pi pi-pencil" rounded text severity="secondary" @click="edit(data)" />
                        <Button icon="pi pi-trash" rounded text severity="danger" :loading="data.loading"
                            @click="destroy(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import PatientFilters from '@/components/PatientFilters.vue';
import PatientForm from '@/components/PatientForm.vue';
import { usePatientsStore } from '@/stores/patients';
import { useAuthStore } from '@/stores/auth';
import { inject, watch } from 'vue';

const { dialog, confirm, toast, router, t } = inject('service')

const store = usePatientsStore()

const auth = useAuthStore()

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