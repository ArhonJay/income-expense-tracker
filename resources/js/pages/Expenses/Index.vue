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
import expensesRoutes from '@/routes/expenses';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard.url(),
            },
            {
                title: 'Expense',
                href: expensesRoutes.index.url(),
            },
        ],
    },
});

const props = defineProps<{
    expenses: Array<{
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
    form.post(expensesRoutes.store.url(), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const filterCategory = (category: string) => {
    router.get(
        expensesRoutes.index.url(),
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
        expensesRoutes.index.url(),
        {
            category: category,
            search: searchInput.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const exportUrl = computed(() => {
    return (
        '/expenses/export?' +
        new URLSearchParams({
            category: props.filters?.category || '',
            search: props.filters?.search || '',
        }).toString()
    );
});

const editingExpense = ref<number | null>(null);
const editForm = useForm({
    category: '',
    amount: '',
    description: '',
    date: '',
});

const openEditModal = (expense: any) => {
    editingExpense.value = expense.id;
    editForm.category = expense.category;
    editForm.amount = expense.amount;
    editForm.description = expense.description || '';
    editForm.date = expense.date;
};

const closeEditModal = () => {
    editingExpense.value = null;
    editForm.reset();
};

const updateExpense = () => {
    if (!editingExpense.value) {
        return;
    }

    editForm.put(expensesRoutes.update.url(editingExpense.value), {
        onSuccess: () => closeEditModal(),
    });
};

const deleteExpense = (id: number) => {
    if (confirm('Are you sure you want to delete this expense?')) {
        router.delete(expensesRoutes.destroy.url(id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Expense" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="grid gap-4 md:grid-cols-3">
            <!-- Add Form -->
            <div class="md:col-span-1">
                <Card>
                    <CardHeader>
                        <CardTitle>Add Expense</CardTitle>
                        <CardDescription
                            >Record a new expense entry.</CardDescription
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
                                    placeholder="e.g. Groceries, Utilities"
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
                                    placeholder="Notes about this expense"
                                />
                            </div>
                            <Button
                                type="submit"
                                class="w-full"
                                :disabled="form.processing"
                                >Save Expense</Button
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
                            <CardTitle>Expense Records</CardTitle>
                            <CardDescription
                                >A list of all your recorded
                                expenses.</CardDescription
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
                                        v-for="expense in expenses"
                                        :key="expense.id"
                                        class="border-b transition-colors hover:bg-muted/50"
                                    >
                                        <td class="p-4 align-middle">
                                            {{ expense.date }}
                                        </td>
                                        <td class="p-4 align-middle">
                                            <div
                                                class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                                            >
                                                {{ expense.category }}
                                            </div>
                                        </td>
                                        <td
                                            class="p-4 align-middle text-muted-foreground"
                                        >
                                            {{ expense.description || '-' }}
                                        </td>
                                        <td
                                            class="p-4 text-right align-middle font-medium text-rose-600 dark:text-rose-400"
                                        >
                                            -₱{{
                                                Number(expense.amount).toFixed(
                                                    2,
                                                )
                                            }}
                                        </td>
                                        <td class="p-4 text-right align-middle">
                                            <div class="flex justify-end gap-2">
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    @click="
                                                        openEditModal(expense)
                                                    "
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    class="text-destructive"
                                                    @click="
                                                        deleteExpense(
                                                            expense.id,
                                                        )
                                                    "
                                                >
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="expenses.length === 0">
                                        <td
                                            colspan="5"
                                            class="h-24 text-center align-middle text-muted-foreground"
                                        >
                                            No expense records found.
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
            :open="!!editingExpense"
            @update:open="(open: boolean) => !open && closeEditModal()"
        >
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Edit Expense</DialogTitle>
                    <DialogDescription>
                        Make changes to your expense record here.
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="updateExpense" class="space-y-4 py-4">
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
