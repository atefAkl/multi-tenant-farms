<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $farms = Farm::with(['blocks'])->get();

        return response()->json([
            'farms' => $farms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'owner' => 'nullable|string|max:255',
            'size' => 'nullable|numeric|min:0',
        ]);

        $farm = Farm::create([
            'tenant_id' => tenant()->id,
            'name' => $request->name,
            'location' => $request->location,
            'owner' => $request->owner,
            'size' => $request->size,
        ]);

        return response()->json([
            'message' => 'Farm created successfully',
            'farm' => $farm,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Farm $farm): Response
    {
        $farm->load(['blocks']);

        return response()->json([
            'farm' => $farm,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farm $farm): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'owner' => 'nullable|string|max:255',
            'size' => 'nullable|numeric|min:0',
        ]);

        $farm->update($request->all());

        return response()->json([
            'message' => 'Farm updated successfully',
            'farm' => $farm,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farm $farm): Response
    {
        $farm->delete();

        return response()->json([
            'message' => 'Farm deleted successfully',
        ]);
    }
}
