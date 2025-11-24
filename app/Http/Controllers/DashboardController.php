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
        // Obtener estadísticas básicas
        $stats = [
            'totalProducts' => Product::count(),
            'lowStockProducts' => Product::whereColumn('stock', '<=', 'min_stock')->count(),
            'totalUsers' => User::count(),
            'totalOrders' => Order::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count(),
            'totalCategories' => Category::count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
