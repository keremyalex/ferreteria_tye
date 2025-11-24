<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Dashboard temporal vacío - se llenará más adelante
        return Inertia::render('Dashboard', [
            'stats' => [],
        ]);
    }
}
