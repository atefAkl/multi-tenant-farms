<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Harvest;
use App\Models\PalmTree;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HarvestController extends Controller
{
    public function index(): View
    {
        $harvests = Harvest::with(['palmTree', 'harvestDetails.worker'])->paginate(15);
        return view('tenant.harvests.index', compact('harvests'));
    }

    public function create(): View
    {
        $palmTrees = PalmTree::where('status', 'active')->get();
        $workers = Worker::where('employment_status', 'active')->get();
        return view('tenant.harvests.create', compact('palmTrees', 'workers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'palm_tree_id' => 'required|exists:palm_trees,id',
            'harvest_date' => 'required|date',
            'season' => 'required|string|max:50',
            'total_quantity' => 'required|numeric|min:0',
            'total_revenue' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'workers' => 'nullable|array',
            'workers.*.worker_id' => 'required|exists:workers,id',
            'workers.*.quantity' => 'required|numeric|min:0',
            'workers.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $harvest = Harvest::create([
            'palm_tree_id' => $validated['palm_tree_id'],
            'harvest_date' => $validated['harvest_date'],
            'season' => $validated['season'],
            'total_quantity' => $validated['total_quantity'],
            'total_revenue' => $validated['total_revenue'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Record harvest details for each worker
        if (isset($validated['workers'])) {
            foreach ($validated['workers'] as $workerData) {
                $harvest->harvestDetails()->create([
                    'worker_id' => $workerData['worker_id'],
                    'quantity' => $workerData['quantity'],
                    'unit_price' => $workerData['unit_price'] ?? 0,
                ]);
            }
        }

        return redirect()->route('tenant.harvests.index')
            ->with('success', 'تم إنشاء الحصاد بنجاح');
    }

    public function show(Harvest $harvest): View
    {
        $harvest->load(['palmTree.block.farm', 'harvestDetails.worker']);
        return view('tenant.harvests.show', compact('harvest'));
    }

    public function edit(Harvest $harvest): View
    {
        $palmTrees = PalmTree::where('status', 'active')->get();
        $workers = Worker::where('employment_status', 'active')->get();
        return view('tenant.harvests.edit', compact('harvest', 'palmTrees', 'workers'));
    }

    public function update(Request $request, Harvest $harvest): RedirectResponse
    {
        $validated = $request->validate([
            'palm_tree_id' => 'required|exists:palm_trees,id',
            'harvest_date' => 'required|date',
            'season' => 'required|string|max:50',
            'total_quantity' => 'required|numeric|min:0',
            'total_revenue' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $harvest->update($validated);

        return redirect()->route('tenant.harvests.index')
            ->with('success', 'تم تحديث الحصاد بنجاح');
    }

    public function destroy(Harvest $harvest): RedirectResponse
    {
        $harvest->delete();

        return redirect()->route('tenant.harvests.index')
            ->with('success', 'تم حذف الحصاد بنجاح');
    }
}
