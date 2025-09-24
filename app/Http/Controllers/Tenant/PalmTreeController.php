<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PalmTree;
use App\Models\Block;
use App\Models\PalmStage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PalmTreeController extends Controller
{
    /**
     * Display a listing of the palm trees.
     */
    public function index(): View
    {
        $palmTrees = PalmTree::with(['block', 'stage'])
            ->paginate(15);

        return view('tenant.palm-trees.index', compact('palmTrees'));
    }

    /**
     * Show the form for creating a new palm tree.
     */
    public function create(): View
    {
        $blocks = Block::where('is_active', true)->get();
        $stages = PalmStage::where('is_active', true)->get();

        return view('tenant.palm-trees.create', compact('blocks', 'stages'));
    }

    /**
     * Store a newly created palm tree in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'tree_code' => 'required|string|max:50|unique:palm_trees',
            'row_no' => 'nullable|integer|min:1',
            'col_no' => 'nullable|integer|min:1',
            'stage_id' => 'required|exists:palm_stages,id',
            'variety' => 'nullable|string|max:100',
            'planting_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,dead',
        ]);

        PalmTree::create($validated);

        return redirect()->route('tenant.palm-trees.index')
            ->with('success', 'تم إنشاء شجرة النخيل بنجاح');
    }

    /**
     * Display the specified palm tree.
     */
    public function show(PalmTree $palmTree): View
    {
        $palmTree->load(['block.farm', 'stage', 'inspections.worker', 'treatments.worker', 'harvests']);
        return view('tenant.palm-trees.show', compact('palmTree'));
    }

    /**
     * Show the form for editing the specified palm tree.
     */
    public function edit(PalmTree $palmTree): View
    {
        $blocks = Block::where('is_active', true)->get();
        $stages = PalmStage::where('is_active', true)->get();

        return view('tenant.palm-trees.edit', compact('palmTree', 'blocks', 'stages'));
    }

    /**
     * Update the specified palm tree in storage.
     */
    public function update(Request $request, PalmTree $palmTree): RedirectResponse
    {
        $validated = $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'tree_code' => 'required|string|max:50|unique:palm_trees,tree_code,' . $palmTree->id,
            'row_no' => 'nullable|integer|min:1',
            'col_no' => 'nullable|integer|min:1',
            'stage_id' => 'required|exists:palm_stages,id',
            'variety' => 'nullable|string|max:100',
            'planting_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,dead',
        ]);

        $palmTree->update($validated);

        return redirect()->route('tenant.palm-trees.index')
            ->with('success', 'تم تحديث شجرة النخيل بنجاح');
    }

    /**
     * Remove the specified palm tree from storage.
     */
    public function destroy(PalmTree $palmTree): RedirectResponse
    {
        $palmTree->delete();

        return redirect()->route('tenant.palm-trees.index')
            ->with('success', 'تم حذف شجرة النخيل بنجاح');
    }
}
