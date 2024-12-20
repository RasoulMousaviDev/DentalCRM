<template>
    <div class="card flex flex-col gap-8 justify-center w-max flex-1">
        <span class="text-xl font-medium">
            {{ $t('gender') }}
        </span>
        <Chart type="doughnut" :data="chartData" :options="chartOptions" :plugins="plugins" class="*:h-48 px-4 mx-auto" />
    </div>
</template>

<script setup>
import { useGendersStore } from '@/stores/genders';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { ref, onMounted, inject, computed, reactive } from "vue";

const props = defineProps({ data: { type: Array, default: ({}) } })

const { t } = inject('service')

const store = useGendersStore()

const chartOptions = ref(null);

const chartData = computed(() => ({
    labels: store.items.map(i => t(i)),
    datasets: [
        {
            data: store.items.map(i => props.data[i] || 0),
            backgroundColor: ['#36A2EB', '#FF6384'],
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

            ctx.fillText(t('total'), centerX, centerY - 30);

            const count = Object.values(props.data).reduce((a, b) => a + b, 0)

            const ctx2 = chart.ctx;
            ctx2.save();
            ctx2.font = '20px vazir';
            ctx2.textAlign = 'center';
            ctx2.textBaseline = 'middle';
            ctx2.fillText(count, centerX, centerY + 10);


            ctx.restore();
            ctx2.restore();
        }
    }
])
onMounted(() => {
    chartOptions.value = setChartOptions();
});
</script>
