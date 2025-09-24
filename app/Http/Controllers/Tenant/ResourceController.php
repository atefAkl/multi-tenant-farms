<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resources.
     */
    public function index(): View
    {
        $resources = Resource::with(['movements' => function($query) {
            $query->latest()->take(5);
        }])->paginate(15);

        return view('tenant.resources.index', compact('resources'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tenant.resources.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:50|unique:resources',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'stock_qty' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'min_stock_level' => 'nullable|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Resource::create($validated);

        return redirect()->route('tenant.resources.index')
            ->with('success', 'تم إنشاء المادة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource): View
    {
        $resource->load(['movements.worker']);
        return view('tenant.resources.show', compact('resource'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource): View
    {
        return view('tenant.resources.edit', compact('resource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:50|unique:resources,sku,' . $resource->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'stock_qty' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'min_stock_level' => 'nullable|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $resource->update($validated);

        return redirect()->route('tenant.resources.index')
            ->with('success', 'تم تحديث المادة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource): RedirectResponse
    {
        $resource->delete();

        return redirect()->route('tenant.resources.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }

    /**
     * Adjust stock for a resource.
     */
    public function adjustStock(Request $request, Resource $resource): RedirectResponse
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $quantity = $validated['quantity'];
        $currentStock = $resource->stock_qty;

        switch ($validated['adjustment_type']) {
            case 'add':
                $newStock = $currentStock + $quantity;
                break;
            case 'subtract':
                $newStock = max(0, $currentStock - $quantity);
                break;
            case 'set':
                $newStock = $quantity;
                break;
        }

        $resource->update(['stock_qty' => $newStock]);

        // Log the movement
        $resource->movements()->create([
            'movement_type' => $validated['adjustment_type'] . '_adjustment',
            'quantity' => $quantity,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'worker_id' => auth()->id(),
        ]);

        return back()->with('success', 'تم تعديل المخزون بنجاح');
    }
}
