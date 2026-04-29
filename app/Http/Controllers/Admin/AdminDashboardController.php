<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = Tenant::estadisticasDashboard();

        return view('admin.dashboard', compact('stats'));
    }
}