<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResourceController extends Controller
{
    public function index(): Response
    {
        $resources = Resource::all();
        return response()->json(['resources' => $resources]);
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'sku' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'stock_qty' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $resource = Resource::create([
            'tenant_id' => tenant()->id,
            ...$request->all()
        ]);

        return response()->json(['message' => 'Resource created successfully', 'resource' => $resource], 201);
    }

    public function show(Resource $resource): Response
    {
        return response()->json(['resource' => $resource]);
    }

    public function update(Request $request, Resource $resource): Response
    {
        $request->validate([
            'sku' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'stock_qty' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $resource->update($request->all());
        return response()->json(['message' => 'Resource updated successfully', 'resource' => $resource]);
    }

    public function destroy(Resource $resource): Response
    {
        $resource->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
}
