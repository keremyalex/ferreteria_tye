<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.inventory')->only(['index', 'show']);
        $this->middleware('can:create.inventory')->only(['create', 'store']);
        $this->middleware('can:edit.inventory')->only(['edit', 'update', 'updateStock']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryDetail::with(['product.category', 'product.measurement', 'product.supplier', 'inventory'])
            ->join('products', 'inventory_details.product_id', '=', 'products.id')
            ->select('inventory_details.*');

        // Filtros de búsqueda
        if ($request->filled('search') && !empty(trim($request->search))) {
            $query->where('products.nombre', 'like', '%' . trim($request->search) . '%');
        }

        // Filtro por categoría
        if ($request->filled('category') && !empty($request->category)) {
            $query->whereHas('product.category', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        // Filtro por stock bajo
        if ($request->boolean('low_stock')) {
            $query->whereColumn('cantidad', '<=', 'cantidad_minima');
        }

        // Filtro por sin stock
        if ($request->boolean('no_stock')) {
            $query->where('cantidad', 0);
        }

        // Ordenar resultados
        $sortField = $request->get('sort', 'products.nombre');
        $sortDirection = $request->get('direction', 'asc');
        
        // Mapear campos de ordenamiento
        $sortMappings = [
            'product.nombre' => 'products.nombre',
            'cantidad' => 'inventory_details.cantidad',
            'precio_venta' => 'inventory_details.precio_venta'
        ];
        
        $actualSortField = $sortMappings[$sortField] ?? $sortField;
        $query->orderBy($actualSortField, $sortDirection);

        $inventoryDetails = $query->paginate(15)->withQueryString();

        // Asegurar que los datos estén correctamente formateados
        $inventoryDetails->getCollection()->transform(function ($item) {
            return $item;
        });

        // Obtener categorías para filtros
        $categories = \App\Models\Category::all();

        // Estadísticas rápidas
        $stats = [
            'total_products' => InventoryDetail::count(),
            'low_stock_count' => InventoryDetail::lowStock()->count(),
            'no_stock_count' => InventoryDetail::where('cantidad', 0)->count(),
            'total_value' => InventoryDetail::selectRaw('SUM(cantidad * precio_venta) as total')->first()->total ?? 0,
        ];

        return Inertia::render('Inventory/Index', [
            'inventoryDetails' => $inventoryDetails,
            'categories' => $categories,
            'filters' => [
                'search' => $request->get('search', ''),
                'category' => $request->get('category', ''),
                'low_stock' => $request->boolean('low_stock'),
                'no_stock' => $request->boolean('no_stock'),
                'sort' => $sortField,
                'direction' => $sortDirection
            ],
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with(['category', 'measurement'])->get();
        
        return Inertia::render('Inventory/Create', [
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.product_id' => 'required|exists:products,id',
            'productos.*.cantidad' => 'required|integer|min:0',
            'productos.*.cantidad_minima' => 'required|integer|min:0',
            'productos.*.precio_venta' => 'required|numeric|min:0',
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'productos.required' => 'Debe agregar al menos un producto.',
            'productos.*.product_id.required' => 'El producto es obligatorio.',
            'productos.*.product_id.exists' => 'El producto seleccionado no existe.',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor o igual a 0.',
            'productos.*.cantidad_minima.required' => 'La cantidad mínima es obligatoria.',
            'productos.*.cantidad_minima.integer' => 'La cantidad mínima debe ser un número entero.',
            'productos.*.cantidad_minima.min' => 'La cantidad mínima debe ser mayor o igual a 0.',
            'productos.*.precio_venta.required' => 'El precio de venta es obligatorio.',
            'productos.*.precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'productos.*.precio_venta.min' => 'El precio de venta debe ser mayor o igual a 0.',
        ]);

        // Crear inventario
        $inventory = Inventory::create([
            'fecha' => $validated['fecha'],
        ]);

        // Crear detalles de inventario
        foreach ($validated['productos'] as $producto) {
            InventoryDetail::create([
                'inventory_id' => $inventory->id,
                'product_id' => $producto['product_id'],
                'cantidad' => $producto['cantidad'],
                'cantidad_minima' => $producto['cantidad_minima'],
                'precio_venta' => $producto['precio_venta'],
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventario registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load(['inventoryDetails.product.category', 'inventoryDetails.product.measurement']);
        
        return Inertia::render('Inventory/Show', [
            'inventory' => $inventory,
        ]);
    }

    /**
     * Update stock for a specific product
     */
    public function updateStock(Request $request, InventoryDetail $inventoryDetail)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:0',
            'cantidad_minima' => 'required|integer|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ], [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor o igual a 0.',
            'cantidad_minima.required' => 'La cantidad mínima es obligatoria.',
            'cantidad_minima.integer' => 'La cantidad mínima debe ser un número entero.',
            'cantidad_minima.min' => 'La cantidad mínima debe ser mayor o igual a 0.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'precio_venta.min' => 'El precio de venta debe ser mayor o igual a 0.',
            'motivo.required' => 'El motivo es obligatorio.',
        ]);

        $inventoryDetail->update([
            'cantidad' => $validated['cantidad'],
            'cantidad_minima' => $validated['cantidad_minima'],
            'precio_venta' => $validated['precio_venta'],
        ]);

        return back()->with('success', 'Stock actualizado correctamente.');
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts()
    {
        $lowStockProducts = InventoryDetail::with(['product.category'])
            ->lowStock()
            ->get();

        return response()->json($lowStockProducts);
    }
}