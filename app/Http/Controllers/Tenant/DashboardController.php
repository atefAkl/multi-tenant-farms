<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the tenant dashboard.
     */
    public function index(): View
    {
        // Get dashboard statistics
        $stats = [
            'total_farms' => \App\Models\Farm::count(),
            'total_blocks' => \App\Models\Block::count(),
            'total_palm_trees' => \App\Models\PalmTree::count(),
            'total_workers' => \App\Models\Worker::count(),
            'recent_inspections' => \App\Models\Inspection::with('palmTree', 'worker')->latest()->take(5)->get(),
            'recent_harvests' => \App\Models\Harvest::with('palmTree')->latest()->take(5)->get(),
        ];

        return view('tenant.dashboard', compact('stats'));
    }
}
