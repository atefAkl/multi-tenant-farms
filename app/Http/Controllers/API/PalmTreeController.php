<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PalmTree;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PalmTreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $palmTrees = PalmTree::with(['block', 'stage'])
            ->when(request('block_id'), fn($q) => $q->where('block_id', request('block_id')))
            ->when(request('stage_id'), fn($q) => $q->where('stage_id', request('stage_id')))
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->get();

        return response()->json([
            'palm_trees' => $palmTrees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'tree_code' => 'required|string|max:255',
            'row_no' => 'nullable|integer|min:1',
            'col_no' => 'nullable|integer|min:1',
            'stage_id' => 'nullable|exists:palm_stages,id',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'status' => 'required|in:healthy,sick,dead,needs_maintenance',
            'gender' => 'required|in:M,F',
        ]);

        $palmTree = PalmTree::create([
            'tenant_id' => tenant()->id,
            'block_id' => $request->block_id,
            'tree_code' => $request->tree_code,
            'row_no' => $request->row_no,
            'col_no' => $request->col_no,
            'stage_id' => $request->stage_id,
            'variety' => $request->variety,
            'planting_date' => $request->planting_date,
            'status' => $request->status,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'message' => 'Palm tree created successfully',
            'palm_tree' => $palmTree,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PalmTree $palmTree): Response
    {
        $palmTree->load(['block', 'stage', 'inspections', 'treatments', 'harvests']);

        return response()->json([
            'palm_tree' => $palmTree,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PalmTree $palmTree): Response
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'tree_code' => 'required|string|max:255',
            'row_no' => 'nullable|integer|min:1',
            'col_no' => 'nullable|integer|min:1',
            'stage_id' => 'nullable|exists:palm_stages,id',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'status' => 'required|in:healthy,sick,dead,needs_maintenance',
            'gender' => 'required|in:M,F',
        ]);

        $palmTree->update($request->all());

        return response()->json([
            'message' => 'Palm tree updated successfully',
            'palm_tree' => $palmTree,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PalmTree $palmTree): Response
    {
        $palmTree->delete();

        return response()->json([
            'message' => 'Palm tree deleted successfully',
        ]);
    }

    /**
     * Get dashboard statistics.
     */
    public function dashboardStats(): Response
    {
        $stats = [
            'total_trees' => PalmTree::count(),
            'healthy_trees' => PalmTree::where('status', 'healthy')->count(),
            'sick_trees' => PalmTree::where('status', 'sick')->count(),
            'needs_maintenance' => PalmTree::where('status', 'needs_maintenance')->count(),
            'male_trees' => PalmTree::where('gender', 'M')->count(),
            'female_trees' => PalmTree::where('gender', 'F')->count(),
        ];

        return response()->json([
            'stats' => $stats,
        ]);
    }
}
