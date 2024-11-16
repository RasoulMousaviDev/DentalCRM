<template>
    <div class="card flex flex-col gap-8 justify-center w-max">
        <span class="text-xl font-medium">
            {{ $t('call-status') }}
        </span>
        <Chart type="pie" :data="chartData" :options="chartOptions" :plugins="plugins" class="h-80 px-4 mx-auto" />
    </div>
</template>

<script setup>
import { useCallStatuesStore } from '@/stores/call-statuses';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { ref, onMounted, inject, computed, reactive } from "vue";
const props = defineProps({ data: { type: Array, default: ({}) } })

const store = useCallStatuesStore()
store.index().then(() => {
    Object.entries(props.data).forEach(([id, count]) => {
        const item = store.items.find(i => i.id == id)
        item.count = count
    })
})
const chartOptions = ref(null);
const backgroundColor = [
    "#4F8BD8", "#1BD8FF", "#D8A11B", "#1BD84F", "#D883A0",
    "#D81B8D", "#1BB8D8", "#D84F1B", "#8B1BD8", "#D81B33",
    "#D84F1B", "#1BD84F", "#1B4FD8", "#D81B6A", "#6A1BD8",
    "#1BD8C6", "#C6D81B", "#D87A1B", "#1BD88F", "#6A1BD8",
]
const chartData = computed(() => ({
    labels: store.items.map(i => i.value),
    datasets: [
        {
            data: store.items.map(i => i.count),
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
                    size: 16,
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
