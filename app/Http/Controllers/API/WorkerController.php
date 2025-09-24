<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WorkerController extends Controller
{
    public function index(): Response
    {
        $workers = Worker::all();
        return response()->json(['workers' => $workers]);
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'farm_id' => 'nullable|exists:farms,id',
            'block_id' => 'nullable|exists:blocks,id',
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'role_in_farm' => 'required|string|max:255',
            'employment_status' => 'required|in:active,inactive,terminated',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $worker = Worker::create([
            'tenant_id' => tenant()->id,
            ...$request->all()
        ]);

        return response()->json(['message' => 'Worker created successfully', 'worker' => $worker], 201);
    }

    public function show(Worker $worker): Response
    {
        return response()->json(['worker' => $worker]);
    }

    public function update(Request $request, Worker $worker): Response
    {
        $request->validate([
            'farm_id' => 'nullable|exists:farms,id',
            'block_id' => 'nullable|exists:blocks,id',
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'role_in_farm' => 'required|string|max:255',
            'employment_status' => 'required|in:active,inactive,terminated',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $worker->update($request->all());
        return response()->json(['message' => 'Worker updated successfully', 'worker' => $worker]);
    }

    public function destroy(Worker $worker): Response
    {
        $worker->delete();
        return response()->json(['message' => 'Worker deleted successfully']);
    }
}
