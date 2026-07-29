<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncomeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = $request->user()->incomes();

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

        return Inertia::render('Incomes/Index', [
            'incomes' => $query->latest('date')->get(),
            'filters' => $request->only(['category', 'search']),
            'categories' => $request->user()->incomes()->select('category')->distinct()->pluck('category'),
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

        $request->user()->incomes()->create($validated);

        return redirect()->back()->with('success', 'Income added successfully.');
    }

    public function update(Request $request, Income $income): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $income->update($validated);

        return redirect()->back()->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income): RedirectResponse
    {
        $income->delete();

        return redirect()->back()->with('success', 'Income deleted successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=incomes.csv',
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

            $query = $request->user()->incomes();
            if ($request->has('category') && $request->category !== 'All') {
                $query->where('category', $request->category);
            }

            $query->latest('date')->chunk(100, function ($incomes) use ($file) {
                foreach ($incomes as $income) {
                    fputcsv($file, [
                        $income->id,
                        $income->category,
                        $income->amount,
                        $income->description,
                        $income->date,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
