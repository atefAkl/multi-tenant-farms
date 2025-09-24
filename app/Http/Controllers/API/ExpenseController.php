<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseController extends Controller
{
    public function index(): Response
    {
        $expenses = Expense::all();
        return response()->json(['expenses' => $expenses]);
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'paid_to' => 'nullable|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense = Expense::create([
            'tenant_id' => tenant()->id,
            ...$request->all()
        ]);

        return response()->json(['message' => 'Expense created successfully', 'expense' => $expense], 201);
    }

    public function show(Expense $expense): Response
    {
        return response()->json(['expense' => $expense]);
    }

    public function update(Request $request, Expense $expense): Response
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'paid_to' => 'nullable|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense->update($request->all());
        return response()->json(['message' => 'Expense updated successfully', 'expense' => $expense]);
    }

    public function destroy(Expense $expense): Response
    {
        $expense->delete();
        return response()->json(['message' => 'Expense deleted successfully']);
    }
}
