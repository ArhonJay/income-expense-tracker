<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    PieController,
    DoughnutController,
    BarController,
} from 'chart.js';
import { ref, computed } from 'vue';
import { Bar, Pie, Doughnut } from 'vue-chartjs';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { dashboard } from '@/routes';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    PieController,
    DoughnutController,
    BarController,
);

const props = defineProps<{
    totalIncome: number;
    totalExpense: number;
    remaining: number;
    incomeByCategory: Record<string, number>;
    expenseByCategory: Record<string, number>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const chartType = ref<'pie' | 'doughnut' | 'bar'>('bar');
const viewMode = ref('expense'); // 'expense' or 'income'

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount);
};

// Generate random colors for categories
const generateColors = (count: number) => {
    const colors = [];

    for (let i = 0; i < count; i++) {
        const hue = (i * 137.508) % 360; // Use golden angle approximation for distinct colors
        colors.push(`hsl(${hue}, 70%, 50%)`);
    }

    return colors;
};

const chartData = computed(() => {
    const data =
        viewMode.value === 'expense'
            ? props.expenseByCategory
            : props.incomeByCategory;
    const labels = Object.keys(data);
    const values = Object.values(data);
    const backgroundColors = generateColors(labels.length);

    return {
        labels,
        datasets: [
            {
                label:
                    viewMode.value === 'expense'
                        ? 'Expenses by Category'
                        : 'Income by Category',
                backgroundColor: backgroundColors,
                data: values,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
};
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground"
                        >Total Expense</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-red-600">
                        {{ formatCurrency(props.totalExpense) }}
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground"
                        >Total Income</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-green-600">
                        {{ formatCurrency(props.totalIncome) }}
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground"
                        >Remaining Balance</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-blue-600">
                        {{ formatCurrency(props.remaining) }}
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card class="flex min-h-[500px] flex-1 flex-col">
            <CardHeader
                class="flex flex-row items-center justify-between border-b pb-4"
            >
                <CardTitle>Financial Overview</CardTitle>
                <div class="flex items-center gap-4">
                    <Select
                        :model-value="viewMode"
                        @update:model-value="
                            (val: any) => (viewMode = String(val))
                        "
                    >
                        <SelectTrigger class="w-[150px]">
                            <SelectValue placeholder="Data View" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="expense">Expenses</SelectItem>
                            <SelectItem value="income">Income</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        :model-value="chartType"
                        @update:model-value="
                            (val: any) =>
                                (chartType = val as 'pie' | 'doughnut' | 'bar')
                        "
                    >
                        <SelectTrigger class="w-[150px]">
                            <SelectValue placeholder="Chart Type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="pie">Pie Chart</SelectItem>
                            <SelectItem value="doughnut"
                                >Doughnut Chart</SelectItem
                            >
                            <SelectItem value="bar">Bar Graph</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </CardHeader>
            <CardContent class="flex flex-1 items-center justify-center p-6">
                <div class="h-full min-h-[400px] w-full">
                    <Pie
                        v-if="chartType === 'pie'"
                        :data="chartData"
                        :options="chartOptions"
                    />
                    <Doughnut
                        v-else-if="chartType === 'doughnut'"
                        :data="chartData"
                        :options="chartOptions"
                    />
                    <Bar
                        v-else-if="chartType === 'bar'"
                        :data="chartData"
                        :options="chartOptions"
                    />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
