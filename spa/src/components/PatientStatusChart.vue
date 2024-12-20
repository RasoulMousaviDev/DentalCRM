<template>
    <div class="card flex flex-col gap-8 justify-center w-max flex-1">
        <span class="text-xl font-medium">
            {{ $t('patient-status') }}
        </span>
        <Chart type="doughnut" :data="chartData" :options="chartOptions" :plugins="plugins" class="*:h-48 px-4 mx-auto" />
    </div>
</template>

<script setup>
import { usePatientsStore } from '@/stores/patients';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { ref, onMounted, inject, computed, reactive } from "vue";

const props = defineProps({ data: { type: Array, default: ({}) } })

const { t } = inject('service')

const store = usePatientsStore()

const chartOptions = ref(null);
const backgroundColor = [
    "#1BD8C6", "#C6D81B", "#D87A1B", "#1BD88F", "#6A1BD8",
    "#D81B8D", "#1BB8D8", "#D84F1B", "#8B1BD8", "#D81B33",
    "#D84F1B", "#1BD84F", "#1B4FD8", "#D81B6A", "#6A1BD8",
    "#1BD84F", "#D883A0", "#4F8BD8", "#1BD8FF", "#D8A11B"
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
                position: 'right',
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

        cutout: '60%',

    };
};


const plugins = reactive([
    ChartDataLabels,
    {
        id: 'centerText',
        beforeDraw(chart) {
            const { width } = chart;
            const { height } = chart;
            const ctx = chart.ctx;
            ctx.save();

            ctx.font = '20px vazir';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            const centerX = width / 2;
            const centerY = height / 2;

            ctx.fillText(t('total'), centerX - 60, centerY - 15);

            const count = Object.values(props.data).reduce((a, b) => a + b, 0)

            const ctx2 = chart.ctx;
            ctx2.save();
            ctx2.font = '20px vazir';
            ctx2.textAlign = 'center';
            ctx2.textBaseline = 'middle';
            ctx2.fillText(count, centerX - 60, centerY + 15);


            ctx.restore();
            ctx2.restore();
        }
    }
])
onMounted(() => {
    chartOptions.value = setChartOptions();
});
</script>
