<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TreatmentController extends Controller
{
    public function index(): Response
    {
        $treatments = Treatment::with(['palmTree', 'worker'])->get();
        return response()->json(['treatments' => $treatments]);
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'tree_id' => 'required|exists:palm_trees,id',
            'worker_id' => 'required|exists:workers,id',
            'type' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $treatment = Treatment::create([
            'tenant_id' => tenant()->id,
            'tree_id' => $request->tree_id,
            'worker_id' => $request->worker_id,
            'type' => $request->type,
            'date' => $request->date,
        ]);

        return response()->json(['message' => 'Treatment created successfully', 'treatment' => $treatment], 201);
    }

    public function show(Treatment $treatment): Response
    {
        $treatment->load(['palmTree', 'worker']);
        return response()->json(['treatment' => $treatment]);
    }

    public function update(Request $request, Treatment $treatment): Response
    {
        $request->validate([
            'tree_id' => 'required|exists:palm_trees,id',
            'worker_id' => 'required|exists:workers,id',
            'type' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $treatment->update($request->all());
        return response()->json(['message' => 'Treatment updated successfully', 'treatment' => $treatment]);
    }

    public function destroy(Treatment $treatment): Response
    {
        $treatment->delete();
        return response()->json(['message' => 'Treatment deleted successfully']);
    }
}
