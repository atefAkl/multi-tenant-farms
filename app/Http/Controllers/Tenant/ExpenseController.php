<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::latest()->paginate(15);
        return view('tenant.expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        return view('tenant.expenses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'paid_to' => 'nullable|string|max:255',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'receipt_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'notes' => 'nullable|string',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');

        Expense::create($validated);

        return redirect()->route('tenant.expenses.index')
            ->with('success', 'تم إنشاء المصروف بنجاح');
    }

    public function show(Expense $expense): View
    {
        return view('tenant.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        return view('tenant.expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'paid_to' => 'nullable|string|max:255',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'receipt_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'notes' => 'nullable|string',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');

        $expense->update($validated);

        return redirect()->route('tenant.expenses.index')
            ->with('success', 'تم تحديث المصروف بنجاح');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('tenant.expenses.index')
            ->with('success', 'تم حذف المصروف بنجاح');
    }
}
