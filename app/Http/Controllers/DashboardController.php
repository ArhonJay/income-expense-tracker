<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalIncome = request()->user()->incomes()->sum('amount');
        $totalExpense = request()->user()->expenses()->sum('amount');
        $remaining = $totalIncome - $totalExpense;

        // Group by category for charts
        $incomeByCategory = request()->user()->incomes()->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $expenseByCategory = request()->user()->expenses()->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return Inertia::render('Dashboard', [
            'totalIncome' => (float) $totalIncome,
            'totalExpense' => (float) $totalExpense,
            'remaining' => (float) $remaining,
            'incomeByCategory' => $incomeByCategory,
            'expenseByCategory' => $expenseByCategory,
        ]);
    }
}
