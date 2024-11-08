<template>
    <div class="card flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <span class="text-2xl font-bold ml-auto">
                {{ $t('dashboard') }}
            </span>
            <div class="flex gap-3">
                <InputGroup class="ltr !w-[19rem]">
                    <DatePicker v-model="form.start_date" inputClass="ltr" panelClass="ltr" dateFormat="yy/mm/dd"
                        :max-date="new Date()" />
                    <InputGroupAddon>{{ $t('start-date') }}</InputGroupAddon>
                </InputGroup>
                <InputGroup class="ltr !w-[19rem]">
                    <DatePicker v-model="form.end_date" inputClass="ltr" panelClass="ltr" dateFormat="yy/mm/dd"
                        :max-date="new Date()" />
                    <InputGroupAddon>{{ $t('end-date') }}</InputGroupAddon>
                </InputGroup>
                <Button :label="$t('search')" />
            </div>
        </div>

        <Chart type="bar" :data="chartData" :options="chartOptions" class="h-[20rem]" />
    </div>
</template>


<script setup>
import Chart from 'primevue/chart';

import { reactive, ref, onMounted } from "vue";

const chartData = ref();
const chartOptions = ref();

const setChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);

    return {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [
            {
                label: 'My First dataset',
                backgroundColor: documentStyle.getPropertyValue('--p-cyan-500'),
                borderColor: documentStyle.getPropertyValue('--p-cyan-500'),
                data: [65, 59, 80, 81, 56, 55, 40]
            }
        ]
    };
};
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        indexAxis: 'y',
        maintainAspectRatio: false,
        plugins: {
            // legend: {
            //     labels: {
            //         color: textColor
            //     }
            // }
        },
        // scales: {
        //     x: {
        //         // ticks: {
        //         //     color: textColorSecondary,
        //         //     font: {
        //         //         weight: 500
        //         //     }
        //         // },
        //         // grid: {
        //         //     display: false,
        //         //     drawBorder: false
        //         // }
        //     },
        //     y: {
        //         // ticks: {
        //         //     color: textColorSecondary
        //         // },
        //         // grid: {
        //         //     color: surfaceBorder,
        //         //     drawBorder: false
        //         // }
        //     }
        // }
    };
}
const form = reactive({})

onMounted(() => {
    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
});
</script>



<style lang="scss" scoped></style>