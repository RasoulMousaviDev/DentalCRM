<template>
    <div class="card flex flex-col gap-8 justify-center w-[74%]">
        <span class="text-xl font-medium">
            {{ $t('reception-report') }}
        </span>
        <Chart type="bar" :data="chartData" :options="chartOptions" :plugins="plugins"
            class="w-full h-80 px-4 mx-auto" />
    </div>
</template>

<script setup>
import { useAppointmentsStore } from '@/stores/appointments';
import { useDepositsStore } from '@/stores/deposits';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { ref, onMounted, inject, computed, reactive } from "vue";
const props = defineProps({ data: { type: Array, default: ({}) } })

const appointments = useAppointmentsStore()
const deposits = useDepositsStore()

const statuses = computed(() => ([
    ...appointments.statuses.slice(0, 3),
    ...deposits.statuses,
    ...appointments.statuses.slice(3),
]))

const chartOptions = ref();
const backgroundColor = [
    "#D84F1B", "#1BD84F", "#1B4FD8", "#D81B6A", "#6A1BD8",
    "#1BD8C6", "#C6D81B", "#D87A1B", "#1BD88F", "#6A1BD8",
    "#D81B8D", "#1BB8D8", "#D84F1B", "#8B1BD8", "#D81B33",
    "#1BD84F", "#D883A0", "#4F8BD8", "#1BD8FF", "#D8A11B"
]

const chartData = computed(() => ({
    labels: statuses.value.map(i => i.value.replace('در انتظار', 'نوبت').replace('پرداخت شده', 'بیعانه')),
    datasets: [
        {
            label: ['', ''],
            backgroundColor: backgroundColor.map(b => b + 'cc'),
            data: statuses.value.map(s => props.data[s.id] || 0)
        },

    ]
}))
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        indexAxis: 'y',
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                enabled: false
            },
            datalabels: {
                color: '#fff',
                font: {
                    size: 14,
                    family: 'vazir'
                },
                formatter: (value, context) => {
                    return `${value}`;
                },
                align: 'end',
                textAlign: 'right',
            }
        },
        scales: {
            x: {
                reverse: true,
                ticks: {
                    display: false,
                },
                grid: {
                    display: false,
                }
            },
            y: {
                position: 'right',
                ticks: {
                    color: textColor,
                    font: {
                        size: 14,
                        family: 'vazir'
                    },
                },
                grid: {
                    color: surfaceBorder,
                    drawBorder: false
                }
            }
        }
    };
}

const plugins = reactive([ChartDataLabels])

onMounted(() => {
    chartOptions.value = setChartOptions();
});
</script>
