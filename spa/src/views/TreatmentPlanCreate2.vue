<template>
    <div class="max-w-full flex gap-8 print:gap-0 relative">
        <div class="max-w-[calc(100%_-_24rem)] print:!max-w-0 grow flex flex-col gap-8">
            <SelectTooth v-model="tooth" :errors="errors.tooths"/>
            <SelectTreatment v-if="tooth" v-model="treatment" :tooth="form.tooths[tooth]" />
            <Services v-if="treatment" v-model="form.tooths[tooth][treatment]" :treatment="treatment" />
            <template v-if="form.payment.method == 'installments' && form.payment.total_amount > 0">
                <SelectInstallment v-model="form.payment" :errors="errors['payment.months_count']" />
                <ChecksDate v-if="form.payment.months_count" v-model="form.payment" />
            </template>
        </div>
        <div class="w-96 shrink-0">
            <div class="sticky top-24 flex flex-col gap-8">
                <SelectPatient v-model="form.patient" :errors="errors['patient.id']" />
                <RequestedServices :tooths="form.tooths" @total-amount="form.payment.total_amount = $event" />
                <SelectPaymentMethod v-model="form.payment" />

                <div v-if="!readonly && auth.user?.role?.name != 'phone-consultant'" class="flex gap-3 print:hidden">
                    <Button icon="pi pi-refresh" :label="$t('restart')" severity="warn" class="shrink-0"
                        @click="handleReset()" />
                    <Button icon="pi pi-save" :label="$t('save-information')" class="grow" severity="success"
                        :loading="loading" @click="handleSubmit()" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import ChecksDate from '@/components/TreatmentPlan/ChecksDate.vue';
import RequestedServices from '@/components/TreatmentPlan/RequestedServices.vue';
import SelectInstallment from '@/components/TreatmentPlan/SelectInstallment.vue';
import SelectPatient from '@/components/TreatmentPlan/SelectPatient.vue';
import SelectPaymentMethod from '@/components/TreatmentPlan/SelectPaymentMethod.vue';
import SelectTooth from '@/components/TreatmentPlan/SelectTooth.vue';
import SelectTreatment from '@/components/TreatmentPlan/SelectTreatment.vue';
import Services from '@/components/TreatmentPlan/Services.vue';
import MyDate from '@/utils/MyDate';
import { useAuthStore } from '@/stores/auth';
import { useTreatmentPlansStore } from '@/stores/treatment-plans';
import { useTreatmentsStore } from '@/stores/treatments';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';
import { usePatientsStore } from '@/stores/patients';

const auth = useAuthStore()

const { router, route, t, toast } = inject("service");

const { patient } = route.query;

const { id } = route.params;

const readonly = ref(!!id);

const form = reactive({
    patient: {
        id: +patient || null,
        visit_type: "in-person",
        desc: ""
    },
    tooths: {},
    payment: {
        method: "cash",
        discount_amount: 0,
        total_amount: 0,
        final_amount: computed(() => form.payment.total_amount - form.payment.discount_amount),
        start_date: new MyDate(),
        interest_percent: 0
    },
})

const tooth = ref()
const treatment = ref()

const treatments = useTreatmentsStore();
if (treatments.items.length < 1)
    treatments.index();

watch(tooth, (v) => {
    Object.entries(form.tooths).forEach(([tooth, value]) => {
        // Object.entries(value).forEach(([service, v]) => {
        //     if (Object.keys(v).length < 1) delete form.tooths[tooth][service]
        // })
        if (Object.keys(value).length < 1) delete form.tooths[tooth]
    })
    if (v && !form.tooths.hasOwnProperty(v))
        form.tooths[v] = {}

    treatment.value = null
    delete errors.value.tooths
})

watch(treatment, (v) => {
    Object.entries(form.tooths[tooth.value]).forEach(([key, value]) => {
        if (Object.keys(value).length < 1) delete form.tooths[tooth.value][key]
    })
    if (v && !form.tooths[tooth.value].hasOwnProperty(v.id))
        form.tooths[tooth.value][v] = {}
})

watch(() => form.patient.id , () => delete errors.value['patient.id'])
watch(() => form.payment.months_count , () => delete errors.value['payment.months_count'])

const loading = ref(true);

const store = useTreatmentPlansStore();

const handleSubmit = async () => {
    loading.value = true;

    const { status, statusText, data } = await store.store(form);

    loading.value = false;

    if (statusText === "OK") {
        if (patient)
            router.replace({ name: "Patient", params: { id: patient } });
        else router.replace({ name: "TreatmentPlans" });
    }
    else if (status === 422)
        errors.value = data.errors
    else
        toast.add({ severity: "error", summary: "Error", detail: data.message, life: 5000, });
};

const handleReset = () => {
    Object.keys(form).forEach((k) => delete form[k]);
    Object.assign(form, {
        patient: {
            id: +patient || null,
            visit_type: "in-person"
        },
        tooths: {},
        payment_method: "cash",
    });
};

const patients = usePatientsStore()

onMounted(async () => {
    if (readonly.value) {
        const { data } = await store.show(id);
        if (data.patient.id)
            await patients.show(data.patient.id);
        Object.assign(form, data);
    } else if (patient) {
        const p = patients.items.find((item) => item.id == patient);
        if (!p) {
            patients.filters.id = patient;
            await patients.index();
            form.patient.id = +patient;
        }
    }

    loading.value = false;
});
</script>

<style lang="scss"></style>