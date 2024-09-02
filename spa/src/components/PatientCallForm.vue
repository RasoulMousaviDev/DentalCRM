<template>
    <form @submit.prevent="handleSubmit()" class="grid grid-cols-2 gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('call_status') }}</label>
            <Select v-model="form.status" :options="[]" :loading="false" optionValue="id" fluid checkmark
                class="has-[+small]:!border-red-500">
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="[].find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
            <small v-if="errors.status" v-text="errors.status[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 grow">
            <label class="has-[+*+small]:text-red-500">{{ $t('mobile') }}</label>
            <Select v-model="form.mobile" :options="mobiles" :optionLabel="(opt) => $t(opt)" fluid checkmark
                class="has-[+small]:!border-red-500" />
            <small v-if="errors.mobile" v-text="errors.mobile[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2 col-span-2">
            <label class="has-[+*+small]:text-red-500"> {{ $t('desc') }}</label>
            <Textarea v-model="form.desc" fluid rows="5" cols="30" />
            <small v-if="errors.desc" v-text="errors.desc[0]" class="text-red-500" />
        </div>
        <hr class="col-span-2">
        <div class="flex flex-col gap-2" :class="{ 'col-span-2': !followup }">
            <label class="has-[+*+small]:text-red-500"> {{ $t('patient_status') }}</label>
            <Select v-model="form.patient_status" :options="[]" :loading="false" optionValue="id" fluid checkmark
                class="has-[+small]:!border-red-500">
                <template #value="{ value }">
                    <Tag v-if="value" class="text-xs" v-bind="[].find(({ id }) => value == id)" />
                </template>
                <template #option="{ option }">
                    <Tag v-bind="option" class="text-xs" />
                </template>
            </Select>
            <small v-if="errors.patient_status" v-text="errors.patient_status[0]" class="text-red-500" />
        </div>
        <template v-if="followup">
            <div class="flex flex-col gap-2">
                <label class="has-[+*+small]:text-red-500">{{ $t('followup-date') }}</label>
                <DatePicker v-model="date" class="w-full [&>input]:has-[+small]:!border-red-500" inputClass="ltr"
                    panelClass="ltr -translate-x-10" dateFormat="yy/mm/dd" showTime hourFormat="24" />
                <small v-if="errors.followup_date" v-text="errors.followup_date[0]" class="text-red-500" />
            </div>
            <div class="flex flex-col gap-2 col-span-2">
                <label class="has-[+*+small]:text-red-500"> {{ $t('followup-desc') }}</label>
                <Textarea v-model="form.followup_desc" fluid rows="5" cols="30" />
                <small v-if="errors.followup_desc" v-text="errors.followup_desc[0]" class="text-red-500" />
            </div>
        </template>
        <div class="flex justify-between col-span-2 gap-2 mt-8">
            <Button :label="$t('back')" severity="secondary" @click="dialogRef.close()" />
            <Button icon="pi pi-save" :label="$t('save')" type="submit" severity="success" :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue';
import { inject, reactive, ref } from 'vue';

const dialogRef = inject('dialogRef')

const form = reactive({})
const errors = ref({})
const loading = ref(false)
const followup = ref(true)

const date = computed({
    get: () => {
        if (form.due_date) {
            const dt = form.due_date.split(' ');
            const d = dt[0].split('/');
            const t = dt[1].split(':');
            return new Date(d[0], d[1] - 1, d[2], t[0], t[1]);
        }
        return null
    },
    set: (v) => form.due_date = [
        v.getFullYear(),
        ('0' + (v.getMonth() + 1)).slice(-2),
        ('0' + v.getDate()).slice(-2)
    ].join('/') + ' ' + [v.getHours(), v.getMinutes()].join(':')
})
</script>

<style lang="scss" scoped></style>