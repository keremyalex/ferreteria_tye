<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\PageVisit;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Estadísticas principales
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalCategories = Category::count();
        
        // Inventario
        $totalInventory = Inventory::count();
        $lowStockProducts = Inventory::whereRaw('cantidad_actual <= cantidad_minima')->count();
        $criticalStockProducts = Inventory::whereRaw('cantidad_actual <= (cantidad_minima * 0.5)')->count();
        
        // Calcular valor del stock usando precio de venta (o precio promedio de compras)
        try {
            $totalStockValue = DB::table('inventory')
                ->join('products', 'inventory.producto_id', '=', 'products.id')
                ->leftJoin('purchase_details', 'products.id', '=', 'purchase_details.product_id')
                ->selectRaw('
                    SUM(
                        inventory.cantidad_actual * 
                        COALESCE(
                            (SELECT AVG(pd.precio) 
                             FROM purchase_details pd 
                             WHERE pd.product_id = products.id 
                             LIMIT 5), 
                            products.precio_venta * 0.7
                        )
                    ) as total_value
                ')
                ->value('total_value') ?? 0;
        } catch (\Exception $e) {
            // Fallback: usar precio de venta con descuento estimado del 30%
            $totalStockValue = Inventory::join('products', 'inventory.producto_id', '=', 'products.id')
                ->sum(DB::raw('inventory.cantidad_actual * (products.precio_venta * 0.7)'));
        }
        
        // Órdenes
        $totalOrders = Order::count();
        $ordersThisMonth = Order::where('created_at', '>=', $currentMonth)->count();
        $ordersLastMonth = Order::where('created_at', '>=', $previousMonth)
            ->where('created_at', '<', $currentMonth)->count();
        $ordersGrowth = $ordersLastMonth > 0 ? (($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100 : 0;
        
        // Ingresos del mes
        $monthlyRevenue = Order::where('estado', 'completado')
            ->where('created_at', '>=', $currentMonth)
            ->sum('total');
            
        $lastMonthRevenue = Order::where('estado', 'completado')
            ->where('created_at', '>=', $previousMonth)
            ->where('created_at', '<', $currentMonth)
            ->sum('total');
            
        $revenueGrowth = $lastMonthRevenue > 0 ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        
        // Compras
        $totalPurchases = Purchase::count();
        $pendingPurchases = Purchase::where('estado', 'pendiente')->count();
        
        // Gráfico de ventas por día (últimos 7 días)
        try {
            $salesChart = Order::where('created_at', '>=', Carbon::now()->subDays(7))
                ->where('estado', 'completado')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item->date)->format('d/m'),
                        'orders' => $item->orders,
                        'revenue' => floatval($item->revenue)
                    ];
                });
        } catch (\Exception $e) {
            $salesChart = collect();
        }
            
        // Completar días faltantes con 0
        $completeSalesChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('d/m');
            $found = $salesChart->firstWhere('date', $dateStr);
            
            $completeSalesChart[] = [
                'date' => $dateStr,
                'orders' => $found ? $found['orders'] : 0,
                'revenue' => $found ? $found['revenue'] : 0
            ];
        }
        
        // Top productos más vendidos
        try {
            $topProducts = DB::table('order_items')
                ->join('orders', 'order_items.orden_id', '=', 'orders.id')
                ->join('products', 'order_items.producto_id', '=', 'products.id')
                ->where('orders.estado', 'completado')
                ->where('orders.created_at', '>=', $currentMonth)
                ->groupBy('order_items.producto_id', 'products.nombre')
                ->selectRaw('products.nombre, SUM(order_items.cantidad) as total_sold, SUM(order_items.cantidad * order_items.precio) as revenue')
                ->orderBy('total_sold', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $topProducts = collect();
        }
            
        // Distribución de inventario por categoría
        try {
            $inventoryByCategory = DB::table('inventory')
                ->join('products', 'inventory.producto_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->groupBy('categories.id', 'categories.nombre')
                ->selectRaw('categories.nombre as category, COUNT(*) as products_count, SUM(inventory.cantidad_actual) as total_stock')
                ->orderBy('total_stock', 'desc')
                ->get();
        } catch (\Exception $e) {
            $inventoryByCategory = collect();
        }
            
        // Actividad reciente
        $recentActivity = [];
        
        try {
            // Órdenes recientes
            $recentOrders = Order::with('user')
                ->latest()
                ->limit(3)
                ->get()
                ->map(function ($order) {
                    return [
                        'type' => 'order',
                        'icon' => 'shopping-cart',
                        'title' => 'Nueva orden #' . $order->id,
                        'description' => 'Cliente: ' . ($order->user->name ?? 'Invitado'),
                        'amount' => $order->total,
                        'time' => $order->created_at->diffForHumans(),
                        'status' => $order->estado
                    ];
                });
        } catch (\Exception $e) {
            $recentOrders = collect();
        }
            
        try {
            // Movimientos de inventario recientes
            $recentMovements = InventoryMovement::with(['product', 'user'])
                ->latest()
                ->limit(3)
                ->get()
                ->map(function ($movement) {
                    return [
                        'type' => 'movement',
                        'icon' => $movement->tipo === 'entrada' ? 'plus-circle' : ($movement->tipo === 'salida' ? 'minus-circle' : 'refresh'),
                        'title' => ucfirst($movement->tipo) . ' de stock',
                        'description' => $movement->product->nombre . ' - ' . $movement->cantidad . ' unidades',
                        'amount' => null,
                        'time' => $movement->created_at->diffForHumans(),
                        'status' => $movement->estado
                    ];
                });
        } catch (\Exception $e) {
            $recentMovements = collect();
        }
            
        try {
            // Visitas de páginas recientes
            $recentVisits = PageVisit::orderBy('last_visited_at', 'desc')
                ->limit(2)
                ->get()
                ->map(function ($visit) {
                    return [
                        'type' => 'visit',
                        'icon' => 'eye',
                        'title' => 'Página visitada',
                        'description' => $visit->page_name . ' - ' . $visit->visits_count . ' visitas total',
                        'amount' => null,
                        'time' => $visit->last_visited_at->diffForHumans(),
                        'status' => 'info'
                    ];
                });
        } catch (\Exception $e) {
            $recentVisits = collect();
        }
            
        $recentActivity = $recentOrders->concat($recentMovements)->concat($recentVisits)
            ->sortByDesc(function ($item) {
                return $item['time'];
            })
            ->take(8)
            ->values();
        
        return Inertia::render('Dashboard', [
            'stats' => [
                'totalProducts' => $totalProducts,
                'totalUsers' => $totalUsers,
                'totalCategories' => $totalCategories,
                'totalInventory' => $totalInventory,
                'lowStockProducts' => $lowStockProducts,
                'criticalStockProducts' => $criticalStockProducts,
                'totalStockValue' => $totalStockValue,
                'totalOrders' => $totalOrders,
                'ordersThisMonth' => $ordersThisMonth,
                'ordersGrowth' => round($ordersGrowth, 1),
                'monthlyRevenue' => $monthlyRevenue,
                'revenueGrowth' => round($revenueGrowth, 1),
                'totalPurchases' => $totalPurchases,
                'pendingPurchases' => $pendingPurchases
            ],
            'charts' => [
                'sales' => $completeSalesChart,
                'topProducts' => $topProducts,
                'inventoryByCategory' => $inventoryByCategory
            ],
            'recentActivity' => $recentActivity
        ]);
    }
}
