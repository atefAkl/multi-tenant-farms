<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $tenants = Tenant::all();

        return response()->json([
            'tenants' => $tenants,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenants',
            'domain' => 'required|string|max:255|unique:tenants',
        ]);

        $tenant = Tenant::create([
            'name' => $request->name,
            'email' => $request->email,
            'domain' => $request->domain,
        ]);

        return response()->json([
            'message' => 'Tenant created successfully',
            'tenant' => $tenant,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant): Response
    {
        return response()->json([
            'tenant' => $tenant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenants,email,' . $tenant->id,
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $tenant->id,
        ]);

        $tenant->update($request->all());

        return response()->json([
            'message' => 'Tenant updated successfully',
            'tenant' => $tenant,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant): Response
    {
        $tenant->delete();

        return response()->json([
            'message' => 'Tenant deleted successfully',
        ]);
    }
}
