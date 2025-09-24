<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function index(): Response
    {
        $invoices = Invoice::with(['items'])->get();
        return response()->json(['invoices' => $invoices]);
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices',
            'customer_name' => 'required|string|max:255',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => tenant()->id,
            ...$request->all()
        ]);

        return response()->json(['message' => 'Invoice created successfully', 'invoice' => $invoice], 201);
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load(['items']);
        return response()->json(['invoice' => $invoice]);
    }

    public function update(Request $request, Invoice $invoice): Response
    {
        $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $invoice->id,
            'customer_name' => 'required|string|max:255',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $invoice->update($request->all());
        return response()->json(['message' => 'Invoice updated successfully', 'invoice' => $invoice]);
    }

    public function destroy(Invoice $invoice): Response
    {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}
