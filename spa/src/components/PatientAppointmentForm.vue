<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-4 w-full md:w-[30rem]">
        <div class="flex flex-col gap-2 grow">
            <label class="has-[+*+small]:text-red-500">{{ $t('treatment') }}</label>
            <Select v-model="form.treatment" :options="treatments" :optionLabel="(opt) => $t(opt)" fluid checkmark
                class="has-[+small]:!border-red-500" />
            <small v-if="errors.treatment" v-text="errors.treatment[0]" class="text-red-500" />
        </div>
        <div class="flex flex-col gap-2">
            <label class="has-[+*+small]:text-red-500">{{ $t('appointment-date') }}</label>
            <DatePicker v-model="date" class="w-full [&>input]:has-[+small]:!border-red-500" inputClass="ltr"
                panelClass="ltr" dateFormat="yy/mm/dd" showTime hourFormat="24" />
            <small v-if="errors.due_date" v-text="errors.due_date[0]" class="text-red-500" />
        </div>
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