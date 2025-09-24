<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\PalmTree;
use App\Models\Worker;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function index(): View
    {
        $treatments = Treatment::with(['palmTree', 'worker', 'resources'])->paginate(15);
        return view('tenant.treatments.index', compact('treatments'));
    }

    public function create(): View
    {
        $palmTrees = PalmTree::where('status', 'active')->get();
        $workers = Worker::where('employment_status', 'active')->get();
        $resources = Resource::where('stock_qty', '>', 0)->get();
        return view('tenant.treatments.create', compact('palmTrees', 'workers', 'resources'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'palm_tree_id' => 'required|exists:palm_trees,id',
            'worker_id' => 'required|exists:workers,id',
            'treatment_date' => 'required|date',
            'treatment_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'resources' => 'nullable|array',
            'resources.*.resource_id' => 'required|exists:resources,id',
            'resources.*.quantity' => 'required|numeric|min:0.01',
            'cost' => 'nullable|numeric|min:0',
            'effectiveness' => 'nullable|in:excellent,good,fair,poor',
        ]);

        $treatment = Treatment::create([
            'palm_tree_id' => $validated['palm_tree_id'],
            'worker_id' => $validated['worker_id'],
            'treatment_date' => $validated['treatment_date'],
            'treatment_type' => $validated['treatment_type'],
            'description' => $validated['description'] ?? null,
            'cost' => $validated['cost'] ?? null,
            'effectiveness' => $validated['effectiveness'] ?? null,
        ]);

        // Handle resources used in treatment
        if (isset($validated['resources'])) {
            foreach ($validated['resources'] as $resourceData) {
                $resource = Resource::find($resourceData['resource_id']);

                // Check stock availability
                if ($resource->stock_qty < $resourceData['quantity']) {
                    return back()->withInput()
                        ->withErrors(['resources' => "الكمية المطلوبة من {$resource->name} غير متوفرة في المخزون"]);
                }

                // Deduct from stock
                $resource->decrement('stock_qty', $resourceData['quantity']);

                // Record the movement
                $resource->movements()->create([
                    'movement_type' => 'treatment_usage',
                    'quantity' => $resourceData['quantity'],
                    'reason' => "علاج شجرة النخيل - {$treatment->treatment_type}",
                    'notes' => "معالجة: {$treatment->palmTree->tree_code}",
                    'worker_id' => $validated['worker_id'],
                ]);
            }
        }

        return redirect()->route('tenant.treatments.index')
            ->with('success', 'تم إنشاء العلاج بنجاح');
    }

    public function show(Treatment $treatment): View
    {
        $treatment->load(['palmTree.block.farm', 'worker', 'resources']);
        return view('tenant.treatments.show', compact('treatment'));
    }

    public function edit(Treatment $treatment): View
    {
        $palmTrees = PalmTree::where('status', 'active')->get();
        $workers = Worker::where('employment_status', 'active')->get();
        $resources = Resource::where('stock_qty', '>', 0)->get();
        return view('tenant.treatments.edit', compact('treatment', 'palmTrees', 'workers', 'resources'));
    }

    public function update(Request $request, Treatment $treatment): RedirectResponse
    {
        $validated = $request->validate([
            'palm_tree_id' => 'required|exists:palm_trees,id',
            'worker_id' => 'required|exists:workers,id',
            'treatment_date' => 'required|date',
            'treatment_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'effectiveness' => 'nullable|in:excellent,good,fair,poor',
        ]);

        $treatment->update($validated);

        return redirect()->route('tenant.treatments.index')
            ->with('success', 'تم تحديث العلاج بنجاح');
    }

    public function destroy(Treatment $treatment): RedirectResponse
    {
        $treatment->delete();

        return redirect()->route('tenant.treatments.index')
            ->with('success', 'تم حذف العلاج بنجاح');
    }
}
