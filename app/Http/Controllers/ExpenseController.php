<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = $request->user()->expenses();

        if ($request->has('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Expenses/Index', [
            'expenses' => $query->latest('date')->get(),
            'filters' => $request->only(['category', 'search']),
            'categories' => $request->user()->expenses()->select('category')->distinct()->pluck('category'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $request->user()->expenses()->create($validated);

        return redirect()->back()->with('success', 'Expense added successfully.');
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $expense->update($validated);

        return redirect()->back()->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=expenses.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($request) {
            $file = fopen('php://output', 'w');
            if (! is_resource($file)) {
                return;
            }
            fputcsv($file, ['ID', 'Category', 'Amount', 'Description', 'Date']);

            $query = $request->user()->expenses();
            if ($request->has('category') && $request->category !== 'All') {
                $query->where('category', $request->category);
            }

            $query->latest('date')->chunk(100, function ($expenses) use ($file) {
                foreach ($expenses as $expense) {
                    fputcsv($file, [
                        $expense->id,
                        $expense->category,
                        $expense->amount,
                        $expense->description,
                        $expense->date,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->streamDownload($callback, 'expenses.csv', $headers);
    }
}
