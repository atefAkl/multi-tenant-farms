<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\PalmTree;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InspectionController extends Controller
{
    public function index(): View
    {
        $inspections = Inspection::with(['palmTree', 'worker'])->paginate(15);
        return view('tenant.inspections.index', compact('inspections'));
    }

    public function create(): View
    {
        $palmTrees = PalmTree::where('status', 'active')->get();
        $workers = Worker::where('employment_status', 'active')->get();
        return view('tenant.inspections.create', compact('palmTrees', 'workers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'palm_tree_id' => 'required|exists:palm_trees,id',
            'worker_id' => 'required|exists:workers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'health_status' => 'required|in:excellent,good,fair,poor,critical',
            'recommendations' => 'nullable|string',
        ]);

        Inspection::create($validated);

        return redirect()->route('tenant.inspections.index')
            ->with('success', 'تم إنشاء الفحص بنجاح');
    }

    public function show(Inspection $inspection): View
    {
        $inspection->load(['palmTree.block.farm', 'worker']);
        return view('tenant.inspections.show', compact('inspection'));
    }

    public function edit(Inspection $inspection): View
    {
        $palmTrees = PalmTree::where('status', 'active')->get();
        $workers = Worker::where('employment_status', 'active')->get();
        return view('tenant.inspections.edit', compact('inspection', 'palmTrees', 'workers'));
    }

    public function update(Request $request, Inspection $inspection): RedirectResponse
    {
        $validated = $request->validate([
            'palm_tree_id' => 'required|exists:palm_trees,id',
            'worker_id' => 'required|exists:workers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'health_status' => 'required|in:excellent,good,fair,poor,critical',
            'recommendations' => 'nullable|string',
        ]);

        $inspection->update($validated);

        return redirect()->route('tenant.inspections.index')
            ->with('success', 'تم تحديث الفحص بنجاح');
    }

    public function destroy(Inspection $inspection): RedirectResponse
    {
        $inspection->delete();

        return redirect()->route('tenant.inspections.index')
            ->with('success', 'تم حذف الفحص بنجاح');
    }
}
