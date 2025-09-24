<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\API\FarmController as APIFarmController;
use App\Http\Controllers\API\PalmTreeController as APIPalmTreeController;
use App\Http\Controllers\API\InspectionController as APIInspectionController;
use App\Http\Controllers\API\TreatmentController as APITreatmentController;
use App\Http\Controllers\API\HarvestController as APIHarvestController;
use App\Http\Controllers\API\ResourceController as APIResourceController;
use App\Http\Controllers\API\InvoiceController as APIInvoiceController;
use App\Http\Controllers\API\ExpenseController as APIExpenseController;
use App\Http\Controllers\API\WorkerController as APIWorkerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

// Tenant management routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('tenants', TenantController::class);

    // API routes for tenant-specific resources
    Route::apiResource('farms', APIFarmController::class);
    Route::apiResource('palm-trees', APIPalmTreeController::class);
    Route::apiResource('inspections', APIInspectionController::class);
    Route::apiResource('treatments', APITreatmentController::class);
    Route::apiResource('harvests', APIHarvestController::class);
    Route::apiResource('resources', APIResourceController::class);
    Route::apiResource('invoices', APIInvoiceController::class);
    Route::apiResource('expenses', APIExpenseController::class);
    Route::apiResource('workers', APIWorkerController::class);

    // Additional API endpoints
    Route::get('/reports/summary', [APIHarvestController::class, 'summary']);
    Route::get('/dashboard/stats', [APIPalmTreeController::class, 'dashboardStats']);
});
