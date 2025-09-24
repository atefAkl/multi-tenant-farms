<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InspectionController extends Controller
{
    public function index(): Response
    {
        $inspections = Inspection::with(['palmTree', 'worker'])->get();
        return response()->json(['inspections' => $inspections]);
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'tree_id' => 'required|exists:palm_trees,id',
            'worker_id' => 'required|exists:workers,id',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $inspection = Inspection::create([
            'tenant_id' => tenant()->id,
            'tree_id' => $request->tree_id,
            'worker_id' => $request->worker_id,
            'notes' => $request->notes,
            'date' => $request->date,
        ]);

        return response()->json(['message' => 'Inspection created successfully', 'inspection' => $inspection], 201);
    }

    public function show(Inspection $inspection): Response
    {
        $inspection->load(['palmTree', 'worker']);
        return response()->json(['inspection' => $inspection]);
    }

    public function update(Request $request, Inspection $inspection): Response
    {
        $request->validate([
            'tree_id' => 'required|exists:palm_trees,id',
            'worker_id' => 'required|exists:workers,id',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $inspection->update($request->all());
        return response()->json(['message' => 'Inspection updated successfully', 'inspection' => $inspection]);
    }

    public function destroy(Inspection $inspection): Response
    {
        $inspection->delete();
        return response()->json(['message' => 'Inspection deleted successfully']);
    }
}
