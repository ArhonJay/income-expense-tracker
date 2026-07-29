<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { dashboard } from '@/routes';
import incomesRoutes from '@/routes/incomes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard.url(),
            },
            {
                title: 'Income',
                href: incomesRoutes.index.url(),
            },
        ],
    },
});

const props = defineProps<{
    incomes: Array<{
        id: number;
        category: string;
        amount: number | string;
        description: string | null;
        date: string;
    }>;
    filters: {
        category?: string;
        search?: string;
    };
    categories: string[];
}>();

const searchInput = ref(props.filters?.search || '');

const form = useForm({
    category: '',
    amount: '',
    description: '',
    date: new Date().toISOString().split('T')[0],
});

const submit = () => {
    form.post(incomesRoutes.store.url(), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const filterCategory = (category: string) => {
    router.get(
        incomesRoutes.index.url(),
        {
            category: category === 'All' ? undefined : category,
            search: searchInput.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const performSearch = () => {
    // We use the prop's current category to preserve the filter
    const currentCategory = document.querySelector(
        'button[role="combobox"] > span',
    )?.textContent;
    const category =
        currentCategory &&
        currentCategory !== 'Filter by category' &&
        currentCategory !== 'All Categories'
            ? currentCategory
            : undefined;

    router.get(
        incomesRoutes.index.url(),
        {
            category: category,
            search: searchInput.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const exportUrl = computed(() => {
    return (
        '/incomes/export?' +
        new URLSearchParams({
            category: props.filters?.category || '',
            search: props.filters?.search || '',
        }).toString()
    );
});

const editingIncome = ref<number | null>(null);
const editForm = useForm({
    category: '',
    amount: '',
    description: '',
    date: '',
});

const openEditModal = (income: any) => {
    editingIncome.value = income.id;
    editForm.category = income.category;
    editForm.amount = income.amount;
    editForm.description = income.description || '';
    editForm.date = income.date;
};

const closeEditModal = () => {
    editingIncome.value = null;
    editForm.reset();
};

const updateIncome = () => {
    if (!editingIncome.value) {
        return;
    }

    editForm.put(incomesRoutes.update.url(editingIncome.value), {
        onSuccess: () => closeEditModal(),
    });
};

const deleteIncome = (id: number) => {
    if (confirm('Are you sure you want to delete this income?')) {
        router.delete(incomesRoutes.destroy.url(id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Income" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="grid gap-4 md:grid-cols-3">
            <!-- Add Form -->
            <div class="md:col-span-1">
                <Card>
                    <CardHeader>
                        <CardTitle>Add Income</CardTitle>
                        <CardDescription
                            >Record a new income entry.</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="date">Date</Label>
                                <Input
                                    id="date"
                                    type="date"
                                    v-model="form.date"
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="category">Category</Label>
                                <Input
                                    id="category"
                                    type="text"
                                    v-model="form.category"
                                    placeholder="e.g. Salary, Freelance"
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="amount">Amount</Label>
                                <Input
                                    id="amount"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    v-model="form.amount"
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="description"
                                    >Description (Optional)</Label
                                >
                                <Input
                                    id="description"
                                    type="text"
                                    v-model="form.description"
                                    placeholder="Notes about this income"
                                />
                            </div>
                            <Button
                                type="submit"
                                class="w-full"
                                :disabled="form.processing"
                                >Save Income</Button
                            >
                        </form>
                    </CardContent>
                </Card>
            </div>

            <!-- Table -->
            <div class="md:col-span-2">
                <Card class="h-full">
                    <CardHeader
                        class="flex flex-row items-center justify-between"
                    >
                        <div>
                            <CardTitle>Income Records</CardTitle>
                            <CardDescription
                                >A list of all your recorded
                                incomes.</CardDescription
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <Input
                                type="search"
                                placeholder="Search records... (Press Enter)"
                                class="w-[220px]"
                                v-model="searchInput"
                                @keydown.enter="performSearch"
                            />
                            <Button variant="outline" as-child>
                                <a :href="exportUrl"> Export CSV </a>
                            </Button>
                            <Select
                                :model-value="filters.category || 'All'"
                                @update:model-value="
                                    (val: any) => filterCategory(String(val))
                                "
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue
                                        placeholder="Filter by category"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="All"
                                        >All Categories</SelectItem
                                    >
                                    <SelectItem
                                        v-for="cat in categories"
                                        :key="cat"
                                        :value="cat"
                                    >
                                        {{ cat }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="relative w-full overflow-auto">
                            <table class="w-full caption-bottom text-sm">
                                <thead class="[&_tr]:border-b">
                                    <tr
                                        class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                    >
                                        <th
                                            class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                        >
                                            Date
                                        </th>
                                        <th
                                            class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                        >
                                            Category
                                        </th>
                                        <th
                                            class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                        >
                                            Description
                                        </th>
                                        <th
                                            class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                                        >
                                            Amount
                                        </th>
                                        <th
                                            class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                                        >
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="[&_tr:last-child]:border-0">
                                    <tr
                                        v-for="income in incomes"
                                        :key="income.id"
                                        class="border-b transition-colors hover:bg-muted/50"
                                    >
                                        <td class="p-4 align-middle">
                                            {{ income.date }}
                                        </td>
                                        <td class="p-4 align-middle">
                                            <div
                                                class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                                            >
                                                {{ income.category }}
                                            </div>
                                        </td>
                                        <td
                                            class="p-4 align-middle text-muted-foreground"
                                        >
                                            {{ income.description || '-' }}
                                        </td>
                                        <td
                                            class="p-4 text-right align-middle font-medium text-emerald-600 dark:text-emerald-400"
                                        >
                                            +₱{{
                                                Number(income.amount).toFixed(2)
                                            }}
                                        </td>
                                        <td class="p-4 text-right align-middle">
                                            <div class="flex justify-end gap-2">
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    @click="
                                                        openEditModal(income)
                                                    "
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    class="text-destructive"
                                                    @click="
                                                        deleteIncome(income.id)
                                                    "
                                                >
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="incomes.length === 0">
                                        <td
                                            colspan="5"
                                            class="h-24 text-center align-middle text-muted-foreground"
                                        >
                                            No income records found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Edit Modal -->
        <Dialog
            :open="!!editingIncome"
            @update:open="(open: boolean) => !open && closeEditModal()"
        >
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Edit Income</DialogTitle>
                    <DialogDescription>
                        Make changes to your income record here.
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="updateIncome" class="space-y-4 py-4">
                    <div class="space-y-2">
                        <Label for="edit-date">Date</Label>
                        <Input
                            id="edit-date"
                            type="date"
                            v-model="editForm.date"
                            required
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-category">Category</Label>
                        <Input
                            id="edit-category"
                            type="text"
                            v-model="editForm.category"
                            required
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-amount">Amount</Label>
                        <Input
                            id="edit-amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            v-model="editForm.amount"
                            required
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-description">Description</Label>
                        <Input
                            id="edit-description"
                            type="text"
                            v-model="editForm.description"
                        />
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeEditModal"
                            >Cancel</Button
                        >
                        <Button type="submit" :disabled="editForm.processing"
                            >Save changes</Button
                        >
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
