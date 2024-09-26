<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-max">
        <div class="flex flex-col gap-2 grow">
            <label class="has-[+*+small]:text-red-500">{{ $t('tooth-number') }}</label>
            <div class="tooth-set [&_.p-selectbutton]:has-[+small]:border-red-500">
                <span v-for="d in directions" v-text="$t(`jaw.${d}`)" :style="d + ': 0'" />
                <SelectButton v-model="form.tooth" class="grid-cols-[repeat(16,_minmax(0,_1fr))]"
                    :options="(Array(32).keys()).map(i => ({ id: i + 1, label: (i < 8 ? 8 - i : (i < 16 ? i - 7 : (i < 24 ? 24 - i : i - 23))) }))"
                    optionLabel="label" optionValue="id" />
            </div>
            <small v-if="errors.tooth" v-text="errors.tooth[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 grow">
            <label class="has-[+*+small]:text-red-500">{{ $t('treatment') }}</label>
            <MultiSelect v-model="form.treatments" display="chip" :options="treatments.items"
                :loading="treatments.fetching" optionLabel="title" optionValue="id" fluid checkmark
                :placeholder="$t('choose')" :showToggleAll="false" class="has-[+small]:!border-red-500 mx-4" />
            <small v-if="errors.treatments" v-text="errors.treatments[0]" class="text-red-500" />
        </div>
        <div class="flex justify-between gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { useTreatmentPlanDetailsStore } from '@/stores/treatment-plan-details';
import { useTreatmentsStore } from '@/stores/treatments';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';

const directions = reactive(['top', 'right', 'bottom', 'left'])

const { toast, } = inject('service')

const dialogRef = inject('dialogRef')

const treatments = useTreatmentsStore()
treatments.index()

const treatmentPlanDetails = useTreatmentPlanDetailsStore()

const form = reactive({
    tooth: '',
    treatments: []
})
const errors = ref({})
const loading = ref(false)

const handleSubmit = async () => {
    loading.value = true

    const { status, statusText, data } = await treatmentPlanDetails.store(form)

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

</script>

<style lang="scss">
.tooth-set {
    @apply flex items-center justify-center relative -mx-3;

    &>span {
        @apply absolute first:self-center;

        &:nth-child(2) {
            writing-mode: vertical-rl;
        }

        &:nth-child(4) {
            writing-mode: vertical-lr;
            transform: rotate(180deg)
        }
    }


    .p-selectbutton {
        @apply grid gap-y-4 ltr p-8 relative #{!important};

        &::before {
            @apply content-['_'] absolute inset-x-6 self-center border-t border-inherit;
        }

        &::after {
            @apply content-['_'] absolute inset-y-6 justify-self-center border-l border-inherit;
        }



        button {

            &:nth-child(8),
            &:nth-child(24) {
                @apply mr-2;
            }

            &:nth-child(9),
            &:nth-child(25) {
                @apply ml-2;
            }
        }
    }

}
</style>