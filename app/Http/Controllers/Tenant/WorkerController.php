<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkerController extends Controller
{
    /**
     * Display a listing of the workers.
     */
    public function index(): View
    {
        $workers = Worker::with(['farm', 'block'])
            ->paginate(15);

        return view('tenant.workers.index', compact('workers'));
    }

    /**
     * Show the form for creating a new worker.
     */
    public function create(): View
    {
        $farms = Farm::where('is_active', true)->get();

        return view('tenant.workers.create', compact('farms'));
    }

    /**
     * Store a newly created worker in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'farm_id' => 'nullable|exists:farms,id',
            'block_id' => 'nullable|exists:blocks,id',
            'role_in_farm' => 'nullable|string|max:100',
            'employment_status' => 'required|in:active,inactive,terminated',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Worker::create($validated);

        return redirect()->route('tenant.workers.index')
            ->with('success', 'تم إنشاء العامل بنجاح');
    }

    /**
     * Display the specified worker.
     */
    public function show(Worker $worker): View
    {
        $worker->load(['farm', 'block', 'inspections.palmTree', 'treatments.palmTree', 'harvestDetails.harvest']);
        return view('tenant.workers.show', compact('worker'));
    }

    /**
     * Show the form for editing the specified worker.
     */
    public function edit(Worker $worker): View
    {
        $farms = Farm::where('is_active', true)->get();

        return view('tenant.workers.edit', compact('worker', 'farms'));
    }

    /**
     * Update the specified worker in storage.
     */
    public function update(Request $request, Worker $worker): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'farm_id' => 'nullable|exists:farms,id',
            'block_id' => 'nullable|exists:blocks,id',
            'role_in_farm' => 'nullable|string|max:100',
            'employment_status' => 'required|in:active,inactive,terminated',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $worker->update($validated);

        return redirect()->route('tenant.workers.index')
            ->with('success', 'تم تحديث العامل بنجاح');
    }

    /**
     * Remove the specified worker from storage.
     */
    public function destroy(Worker $worker): RedirectResponse
    {
        $worker->delete();

        return redirect()->route('tenant.workers.index')
            ->with('success', 'تم حذف العامل بنجاح');
    }
}
