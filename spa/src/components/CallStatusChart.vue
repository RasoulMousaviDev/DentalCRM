<template>
    <div class="card flex flex-col gap-8 justify-center w-max flex-1">
        <span class="text-xl font-medium">
            {{ $t('call-status') }}
        </span>
        <Chart type="pie" :data="chartData" :options="chartOptions" :plugins="plugins" class="*:h-48 px-4 mx-auto" />
    </div>
</template>

<script setup>
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { useCallsStore } from '@/stores/calls';
import { ref, onMounted, computed, reactive } from "vue";

const props = defineProps({ data: { type: Array, default: ({}) } })

const store = useCallsStore()

const chartOptions = ref(null);
const backgroundColor = [
    "#1BD84F", "#D81B33", "#CCCCCC",
]
const chartData = computed(() => ({
    labels: store.statuses.map(i => i.value),
    datasets: [
        {
            data: store.statuses.map(s => props.data[s.id] || 0),
            backgroundColor,
        }
    ]
}));

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');

    return {
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    cutout: '60%',
                    color: textColor,
                    font: {
                        family: 'vazir'
                    },
                    pointStyle: 'circle',
                    usePointStyle: true
                },

                rtl: true
            },
            tooltip: {
                enabled: false
            },
            datalabels: {
                color: '#fff',
                font: {
                    size: 12,
                    family: 'vazir'
                },
                formatter: (value, context) => {
                    const total = context.dataset.data.reduce((sum, currentValue) => sum + currentValue, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    return percentage + '%';
                },
                align: 'center',
                anchor: 'center',
                textAlign: 'center',
                textBaseline: 'middle'

            }
        },
    };
};


const plugins = reactive([
    ChartDataLabels
])
onMounted(() => {
    chartOptions.value = setChartOptions();
});
</script>
