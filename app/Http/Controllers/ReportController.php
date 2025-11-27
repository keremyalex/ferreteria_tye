<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.reports')->only(['index']);
        $this->middleware('can:view.sales.reports')->only(['sales']);
        $this->middleware('can:view.inventory.reports')->only(['inventory']);
        $this->middleware('can:view.purchases.reports')->only(['purchases']);
    }

    /**
     * Página principal de reportes
     */
    public function index()
    {
        return Inertia::render('Reports/Index');
    }

    /**
     * Reporte de ventas
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Ventas por período
        $salesByPeriod = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 'completado')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders_count, SUM(total) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top clientes
        $topClients = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 'completado')
            ->with(['client', 'user'])
            ->selectRaw('cliente_id, usuario_id, COUNT(*) as orders_count, SUM(total) as total_spent')
            ->groupBy(['cliente_id', 'usuario_id'])
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get()
            ->map(function($order) {
                return [
                    'client_name' => $order->client ? $order->client->nombre : ($order->user ? $order->user->name : 'Cliente no identificado'),
                    'orders_count' => $order->orders_count,
                    'total_spent' => $order->total_spent
                ];
            });

        // Ventas por método de pago
        $salesByPaymentMethod = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 'completado')
            ->selectRaw('metodo_pago, COUNT(*) as orders_count, SUM(total) as total_amount')
            ->groupBy('metodo_pago')
            ->get();

        // Estadísticas generales
        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 'completado')
            ->sum('total');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 'completado')
            ->count();

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return Inertia::render('Reports/Sales', [
            'salesByPeriod' => $salesByPeriod,
            'topClients' => $topClients,
            'salesByPaymentMethod' => $salesByPaymentMethod,
            'stats' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_order_value' => $averageOrderValue,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Reporte de productos
     */
    public function products(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Productos más vendidos
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.orden_id', '=', 'orders.id')
            ->join('products', 'order_items.producto_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.estado', 'completado')
            ->selectRaw('products.id, products.nombre, SUM(order_items.cantidad) as total_sold, SUM(order_items.total) as total_revenue, AVG(order_items.precio) as avg_price')
            ->groupBy(['products.id', 'products.nombre'])
            ->orderBy('total_revenue', 'desc')
            ->limit(20)
            ->get();

        // Productos por categoría
        $productsByCategory = DB::table('order_items')
            ->join('orders', 'order_items.orden_id', '=', 'orders.id')
            ->join('products', 'order_items.producto_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.estado', 'completado')
            ->selectRaw('categories.nombre as category_name, COUNT(DISTINCT products.id) as products_count, SUM(order_items.cantidad) as total_sold, SUM(order_items.total) as total_revenue')
            ->groupBy(['categories.id', 'categories.nombre'])
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Productos con bajo movimiento
        $lowMovementProducts = Product::with(['category'])
            ->leftJoin('order_items', function($join) use ($startDate, $endDate) {
                $join->on('products.id', '=', 'order_items.producto_id')
                    ->whereExists(function($query) use ($startDate, $endDate) {
                        $query->select(DB::raw(1))
                            ->from('orders')
                            ->whereRaw('orders.id = order_items.orden_id')
                            ->whereBetween('orders.created_at', [$startDate, $endDate])
                            ->where('orders.estado', 'completado');
                    });
            })
            ->selectRaw('products.id, products.nombre, products.precio_venta, COALESCE(SUM(order_items.cantidad), 0) as total_sold')
            ->groupBy(['products.id', 'products.nombre', 'products.precio_venta'])
            ->havingRaw('COALESCE(SUM(order_items.cantidad), 0) < 5')
            ->orderByRaw('COALESCE(SUM(order_items.cantidad), 0) asc')
            ->limit(20)
            ->get();

        return Inertia::render('Reports/Products', [
            'topProducts' => $topProducts,
            'productsByCategory' => $productsByCategory,
            'lowMovementProducts' => $lowMovementProducts,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Reporte de inventario
     */
    public function inventory(Request $request)
    {
        // Stock actual
        $currentStock = Inventory::with(['product.category'])
            ->where('cantidad_actual', '>', 0)
            ->orderBy('cantidad_actual', 'desc')
            ->get()
            ->map(function($inventory) {
                return [
                    'product_name' => $inventory->product->nombre,
                    'product_id' => $inventory->product->id,
                    'category' => $inventory->product->category->nombre,
                    'current_stock' => $inventory->cantidad_actual,
                    'min_stock' => $inventory->cantidad_minima,
                    'value' => $inventory->cantidad_actual * $inventory->product->precio_venta,
                    'status' => $inventory->cantidad_actual <= $inventory->cantidad_minima ? 'low' : 'normal'
                ];
            });

        // Productos con stock crítico
        $criticalStock = Inventory::with(['product.category'])
            ->whereRaw('cantidad_actual <= cantidad_minima')
            ->get()
            ->map(function($inventory) {
                return [
                    'product_name' => $inventory->product->nombre,
                    'product_id' => $inventory->product->id,
                    'category' => $inventory->product->category->nombre,
                    'current_stock' => $inventory->cantidad_actual,
                    'min_stock' => $inventory->cantidad_minima,
                    'difference' => $inventory->cantidad_minima - $inventory->cantidad_actual
                ];
            });

        // Valor total del inventario
        $totalInventoryValue = Inventory::join('products', 'inventory.producto_id', '=', 'products.id')
            ->selectRaw('SUM(inventory.cantidad_actual * products.precio_venta) as total_value')
            ->value('total_value') ?? 0;

        // Productos agotados
        $outOfStock = Inventory::with(['product.category'])
            ->where('cantidad_actual', '=', 0)
            ->get()
            ->map(function($inventory) {
                return [
                    'product_name' => $inventory->product->nombre,
                    'product_id' => $inventory->product->id,
                    'category' => $inventory->product->category->nombre
                ];
            });

        return Inertia::render('Reports/Inventory', [
            'currentStock' => $currentStock,
            'criticalStock' => $criticalStock,
            'outOfStock' => $outOfStock,
            'stats' => [
                'total_inventory_value' => $totalInventoryValue,
                'critical_products' => $criticalStock->count(),
                'out_of_stock_products' => $outOfStock->count(),
                'total_products' => $currentStock->count()
            ]
        ]);
    }

    /**
     * Reporte de compras
     */
    public function purchases(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Compras por proveedor
        $purchasesBySupplier = Purchase::with('supplier')
            ->whereBetween('fecha', [$startDate, $endDate])
            ->selectRaw('supplier_id, COUNT(*) as purchases_count, SUM(monto_total) as total_amount')
            ->groupBy('supplier_id')
            ->orderBy('total_amount', 'desc')
            ->get()
            ->map(function($purchase) {
                return [
                    'supplier_name' => $purchase->supplier->nombre_empresa,
                    'purchases_count' => $purchase->purchases_count,
                    'total_amount' => $purchase->total_amount
                ];
            });

        // Compras por período
        $purchasesByPeriod = Purchase::whereBetween('fecha', [$startDate, $endDate])
            ->selectRaw('DATE(fecha) as date, COUNT(*) as purchases_count, SUM(monto_total) as total_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Productos más comprados
        $topPurchasedProducts = DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->whereBetween('purchases.fecha', [$startDate, $endDate])
            ->selectRaw('products.id, products.nombre, SUM(purchase_details.cantidad) as total_purchased, SUM(purchase_details.cantidad * purchase_details.precio) as total_cost, AVG(purchase_details.precio) as avg_price')
            ->groupBy(['products.id', 'products.nombre'])
            ->orderBy('total_cost', 'desc')
            ->limit(20)
            ->get();

        // Estadísticas generales
        $totalPurchases = Purchase::whereBetween('fecha', [$startDate, $endDate])->sum('monto_total');
        $totalPurchaseOrders = Purchase::whereBetween('fecha', [$startDate, $endDate])->count();

        return Inertia::render('Reports/Purchases', [
            'purchasesBySupplier' => $purchasesBySupplier,
            'purchasesByPeriod' => $purchasesByPeriod,
            'topPurchasedProducts' => $topPurchasedProducts,
            'stats' => [
                'total_purchases' => $totalPurchases,
                'total_purchase_orders' => $totalPurchaseOrders,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    public function exportSalesPdf(Request $request)
    {
        // Determinar fechas
        if ($request->has('start_date') && $request->has('end_date')) {
            // Usar fechas específicas
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $periodText = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
        } else {
            // Usar período por defecto
            $period = $request->get('period', '7');
            $startDate = Carbon::now()->subDays($period)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            
            $periodText = match($period) {
                '7' => 'Últimos 7 días',
                '30' => 'Últimos 30 días',
                '90' => 'Últimos 90 días',
                '365' => 'Último año',
                default => "Últimos {$period} días"
            };
        }
        
        // Obtener datos del reporte de ventas
        $salesData = $this->getSalesData($startDate, $endDate);

        $pdf = Pdf::loadView('reports.sales-pdf', [
            'period' => $periodText,
            'totalSales' => $salesData['totalSales'],
            'totalOrders' => $salesData['totalOrders'],
            'averageOrder' => $salesData['averageOrder'],
            'totalProducts' => $salesData['totalProducts'],
            'topProducts' => $salesData['topProducts'],
            'topClients' => $salesData['topClients']
        ]);

        return $pdf->download('reporte-ventas-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getSalesData($startDate, $endDate = null)
    {
        // Si no se proporciona endDate, usar startDate como punto de inicio y ahora como final
        if ($endDate === null) {
            $endDate = Carbon::now()->endOfDay();
        }

        // Total de ventas
        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 'completado')
            ->sum('total');

        // Total de órdenes
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 'completado')
            ->count();

        // Promedio por orden
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Total de productos vendidos
        $totalProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.orden_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.estado', 'completado')
            ->sum('order_items.cantidad');

        // Productos más vendidos
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.orden_id')
            ->join('products', 'products.id', '=', 'order_items.producto_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.estado', 'completado')
            ->select(
                'products.nombre',
                DB::raw('SUM(order_items.cantidad) as total_sold'),
                DB::raw('SUM(order_items.cantidad * order_items.precio) as total_revenue')
            )
            ->groupBy('products.id', 'products.nombre')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Mejores clientes
        $topClients = DB::table('orders')
            ->leftJoin('clients', 'clients.id', '=', 'orders.cliente_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.estado', 'completado')
            ->select(
                DB::raw('COALESCE(clients.nombre, \'Cliente Anónimo\') as cliente_nombre'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('clients.id', 'clients.nombre')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return [
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'averageOrder' => $averageOrder,
            'totalProducts' => $totalProducts,
            'topProducts' => $topProducts,
            'topClients' => $topClients
        ];
    }
}