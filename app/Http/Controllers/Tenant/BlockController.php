<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlockController extends Controller
{
    public function index(): View
    {
        $blocks = Block::with('farm')->paginate(15);
        return view('tenant.blocks.index', compact('blocks'));
    }

    public function create(): View
    {
        $farms = Farm::where('is_active', true)->get();
        return view('tenant.blocks.create', compact('farms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'area' => 'nullable|numeric|min:0',
            'soil_type' => 'nullable|string|max:100',
            'irrigation_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Block::create($validated);

        return redirect()->route('tenant.blocks.index')
            ->with('success', 'تم إنشاء القطعة بنجاح');
    }

    public function show(Block $block): View
    {
        $block->load('farm', 'palmTrees.stage');
        return view('tenant.blocks.show', compact('block'));
    }

    public function edit(Block $block): View
    {
        $farms = Farm::where('is_active', true)->get();
        return view('tenant.blocks.edit', compact('block', 'farms'));
    }

    public function update(Request $request, Block $block): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'area' => 'nullable|numeric|min:0',
            'soil_type' => 'nullable|string|max:100',
            'irrigation_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $block->update($validated);

        return redirect()->route('tenant.blocks.index')
            ->with('success', 'تم تحديث القطعة بنجاح');
    }

    public function destroy(Block $block): RedirectResponse
    {
        $block->delete();

        return redirect()->route('tenant.blocks.index')
            ->with('success', 'تم حذف القطعة بنجاح');
    }
}
