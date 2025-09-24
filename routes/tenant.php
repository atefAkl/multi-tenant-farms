<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\FarmController;
use App\Http\Controllers\Tenant\BlockController;
use App\Http\Controllers\Tenant\PalmTreeController;
use App\Http\Controllers\Tenant\WorkerController;
use App\Http\Controllers\Tenant\InspectionController;
use App\Http\Controllers\Tenant\TreatmentController;
use App\Http\Controllers\Tenant\HarvestController;
use App\Http\Controllers\Tenant\ResourceController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\ExpenseController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('tenant.dashboard');

    // Tenant-specific authentication routes (redirect to central domain)
    Route::get('/login', function () {
        return redirect('/login');
    })->name('tenant.login');

    Route::middleware(['auth', 'verified'])->group(function () {

        // Farms Management
        Route::resource('farms', FarmController::class)->names([
            'index' => 'tenant.farms.index',
            'create' => 'tenant.farms.create',
            'store' => 'tenant.farms.store',
            'show' => 'tenant.farms.show',
            'edit' => 'tenant.farms.edit',
            'update' => 'tenant.farms.update',
            'destroy' => 'tenant.farms.destroy',
        ]);

        // Blocks Management
        Route::resource('blocks', BlockController::class)->names([
            'index' => 'tenant.blocks.index',
            'create' => 'tenant.blocks.create',
            'store' => 'tenant.blocks.store',
            'show' => 'tenant.blocks.show',
            'edit' => 'tenant.blocks.edit',
            'update' => 'tenant.blocks.update',
            'destroy' => 'tenant.blocks.destroy',
        ]);

        // Palm Trees Management
        Route::resource('palm-trees', PalmTreeController::class)->names([
            'index' => 'tenant.palm-trees.index',
            'create' => 'tenant.palm-trees.create',
            'store' => 'tenant.palm-trees.store',
            'show' => 'tenant.palm-trees.show',
            'edit' => 'tenant.palm-trees.edit',
            'update' => 'tenant.palm-trees.update',
            'destroy' => 'tenant.palm-trees.destroy',
        ]);

        // Workers Management
        Route::resource('workers', WorkerController::class)->names([
            'index' => 'tenant.workers.index',
            'create' => 'tenant.workers.create',
            'store' => 'tenant.workers.store',
            'show' => 'tenant.workers.show',
            'edit' => 'tenant.workers.edit',
            'update' => 'tenant.workers.update',
            'destroy' => 'tenant.workers.destroy',
        ]);

        // Inspections Management
        Route::resource('inspections', InspectionController::class)->names([
            'index' => 'tenant.inspections.index',
            'create' => 'tenant.inspections.create',
            'store' => 'tenant.inspections.store',
            'show' => 'tenant.inspections.show',
            'edit' => 'tenant.inspections.edit',
            'update' => 'tenant.inspections.update',
            'destroy' => 'tenant.inspections.destroy',
        ]);

        // Treatments Management
        Route::resource('treatments', TreatmentController::class)->names([
            'index' => 'tenant.treatments.index',
            'create' => 'tenant.treatments.create',
            'store' => 'tenant.treatments.store',
            'show' => 'tenant.treatments.show',
            'edit' => 'tenant.treatments.edit',
            'update' => 'tenant.treatments.update',
            'destroy' => 'tenant.treatments.destroy',
        ]);

        // Harvests Management
        Route::resource('harvests', HarvestController::class)->names([
            'index' => 'tenant.harvests.index',
            'create' => 'tenant.harvests.create',
            'store' => 'tenant.harvests.store',
            'show' => 'tenant.harvests.show',
            'edit' => 'tenant.harvests.edit',
            'update' => 'tenant.harvests.update',
            'destroy' => 'tenant.harvests.destroy',
        ]);

        // Resources/Inventory Management
        Route::resource('resources', ResourceController::class)->names([
            'index' => 'tenant.resources.index',
            'create' => 'tenant.resources.create',
            'store' => 'tenant.resources.store',
            'show' => 'tenant.resources.show',
            'edit' => 'tenant.resources.edit',
            'update' => 'tenant.resources.update',
            'destroy' => 'tenant.resources.destroy',
        ]);

        // Invoices Management
        Route::resource('invoices', InvoiceController::class)->names([
            'index' => 'tenant.invoices.index',
            'create' => 'tenant.invoices.create',
            'store' => 'tenant.invoices.store',
            'show' => 'tenant.invoices.show',
            'edit' => 'tenant.invoices.edit',
            'update' => 'tenant.invoices.update',
            'destroy' => 'tenant.invoices.destroy',
        ]);

        // Expenses Management
        Route::resource('expenses', ExpenseController::class)->names([
            'index' => 'tenant.expenses.index',
            'create' => 'tenant.expenses.create',
            'store' => 'tenant.expenses.store',
            'show' => 'tenant.expenses.show',
            'edit' => 'tenant.expenses.edit',
            'update' => 'tenant.expenses.update',
            'destroy' => 'tenant.expenses.destroy',
        ]);

        // Additional routes for specific actions
        Route::get('/reports', function () {
            return view('tenant.reports.index');
        })->name('tenant.reports.index');

        Route::get('/settings', function () {
            return view('tenant.settings.index');
        })->name('tenant.settings.index');
    });
});
