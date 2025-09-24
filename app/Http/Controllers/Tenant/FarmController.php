<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FarmController extends Controller
{
    /**
     * Display a listing of the farms.
     */
    public function index(): View
    {
        $farms = Farm::with('blocks')->paginate(15);
        return view('tenant.farms.index', compact('farms'));
    }

    /**
     * Show the form for creating a new farm.
     */
    public function create(): View
    {
        return view('tenant.farms.create');
    }

    /**
     * Store a newly created farm in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'owner' => 'nullable|string|max:255',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'coordinates' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Farm::create($validated);

        return redirect()->route('tenant.farms.index')
            ->with('success', 'تم إنشاء المزرعة بنجاح');
    }

    /**
     * Display the specified farm.
     */
    public function show(Farm $farm): View
    {
        $farm->load('blocks.palmTrees');
        return view('tenant.farms.show', compact('farm'));
    }

    /**
     * Show the form for editing the specified farm.
     */
    public function edit(Farm $farm): View
    {
        return view('tenant.farms.edit', compact('farm'));
    }

    /**
     * Update the specified farm in storage.
     */
    public function update(Request $request, Farm $farm): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'owner' => 'nullable|string|max:255',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'coordinates' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $farm->update($validated);

        return redirect()->route('tenant.farms.index')
            ->with('success', 'تم تحديث المزرعة بنجاح');
    }

    /**
     * Remove the specified farm from storage.
     */
    public function destroy(Farm $farm): RedirectResponse
    {
        $farm->delete();

        return redirect()->route('tenant.farms.index')
            ->with('success', 'تم حذف المزرعة بنجاح');
    }
}
