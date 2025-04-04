<template>
    <div class="card">
        <DataTable :value="store.items" tableStyle="min-width: 50rem"
            class="[&_td]:cursor-pointer whitespace-nowrap" row-hover @row-click="showPatient">
            <template #header>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold">{{ $t('follow-ups') }}</span>
                        <Button icon="pi pi-refresh" rounded text :loading="store.fetching" @click="store.index()" />
                        <hr class="grow">
                        </hr>
                    </div>
                    <FollowUpFilters />
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
                    {{ index + 1 }}
                </template>
            </Column>
            <template v-if="$route.name != 'Patient'">
                <Column :field="({ patient: { firstname, lastname } }) => [firstname, lastname].join(' ')"
                    :header="$t('patient-name')" />
                <Column v-if="['super-admin', 'admin'].includes(auth.user?.role?.name)" field="patient.user.name"
                    :header="$t('consultant')" />
            </template>
            <Column
                :field="({ patient: { mobiles, telephone } }) => mobiles.map(({ number }) => number).join(' | ') || telephone"
                :header="$t('mobile')" body-class="ltr !text-left" />
            <Column field="desc" :header="$t('desc')" body-class="whitespace-normal" class="min-w-96" />
            <Column field="due_date" :header="$t('due-date')" bodyClass="ltr" class="w-44" />
            <Column field="status" :header="$t('status')" class="w-32">
                <template #body="{ data: { status } }">
                    <Tag v-bind="status" />
                </template>
            </Column>
            <Column field="created_at" :header="$t('created_at')" bodyClass="ltr" class="w-44" />
            <Column field="updated_at" :header="$t('updated_at')" bodyClass="ltr" class="w-44" />
            <Column :header="$t('actions')" headerClass="[&>div]:justify-end [&>div]:pl-5 w-20">
                <template #body="{ data: { id, status } }">
                    <div class="flex gap-2 pr-2">
                        <i v-if="status.severity == 'success'" class="pi pi-check text-green-500 p-3"></i>
                        <Button v-else icon="pi pi-check" size="small" rounded severity="success" @click="done(id)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import CallForm from '@/components/CallForm.vue';
import FollowUpFilters from '@/components/FollowUpFilters.vue';
import { useAuthStore } from '@/stores/auth';
import { useFollowUpsStore } from '@/stores/follow-ups';
import { inject } from 'vue';

const { route, dialog, t } = inject('service')

const store = useFollowUpsStore()
if (route.name == 'Patient')
    store.filters.patient = route.params.id

store.index()

const auth = useAuthStore()

const done = async (id) => {
    dialog.open(CallForm, {
        props: {
            header: t('createNewCall'), modal: true
        },
        data: id
    })
}
</script>

<style lang="scss" scoped></style>