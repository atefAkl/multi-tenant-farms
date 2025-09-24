<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Harvest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HarvestController extends Controller
{
    public function index(): Response
    {
        $harvests = Harvest::with(['palmTree', 'details'])->get();
        return response()->json(['harvests' => $harvests]);
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'tree_id' => 'required|exists:palm_trees,id',
            'season' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $harvest = Harvest::create([
            'tenant_id' => tenant()->id,
            'tree_id' => $request->tree_id,
            'season' => $request->season,
            'date' => $request->date,
        ]);

        return response()->json(['message' => 'Harvest created successfully', 'harvest' => $harvest], 201);
    }

    public function show(Harvest $harvest): Response
    {
        $harvest->load(['palmTree', 'details']);
        return response()->json(['harvest' => $harvest]);
    }

    public function update(Request $request, Harvest $harvest): Response
    {
        $request->validate([
            'tree_id' => 'required|exists:palm_trees,id',
            'season' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $harvest->update($request->all());
        return response()->json(['message' => 'Harvest updated successfully', 'harvest' => $harvest]);
    }

    public function destroy(Harvest $harvest): Response
    {
        $harvest->delete();
        return response()->json(['message' => 'Harvest deleted successfully']);
    }

    public function summary(): Response
    {
        $summary = [
            'total_harvests' => Harvest::count(),
            'this_season' => Harvest::where('season', date('Y'))->count(),
            'total_quantity' => Harvest::sum('total_quantity'),
        ];

        return response()->json(['summary' => $summary]);
    }
}
