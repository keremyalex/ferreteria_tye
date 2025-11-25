<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $query = Inventory::with(['product.category', 'product.measurement', 'product.supplier']);

        // Filtros de búsqueda
        if ($request->filled('search') && !empty(trim($request->search))) {
            $searchTerm = trim($request->search);
            $query->whereHas('product', function ($q) use ($searchTerm) {
                $q->where('nombre', 'ILIKE', '%' . $searchTerm . '%')
                  ->orWhere('descripcion', 'ILIKE', '%' . $searchTerm . '%');
            });
        }

        // Filtro por categoría
        if ($request->filled('category') && !empty($request->category)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Filtro por stock bajo
        if ($request->boolean('stock_bajo')) {
            $query->stockBajo();
        }

        // Filtro por sin stock
        if ($request->boolean('sin_stock')) {
            $query->sinStock();
        }

        // Ordenar resultados
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'product.nombre') {
            $query->join('products', 'inventory.producto_id', '=', 'products.id')
                  ->orderBy('products.nombre', $sortDirection)
                  ->select('inventory.*');
        } else {
            $validSortFields = ['cantidad_actual', 'cantidad_minima', 'precio_venta', 'id'];
            $sortField = in_array($sortField, $validSortFields) ? $sortField : 'id';
            $query->orderBy($sortField, $sortDirection);
        }

        $inventoryItems = $query->paginate(15)->withQueryString();

        // Obtener categorías para filtros
        $categories = \App\Models\Category::all();

        // Estadísticas rápidas
        $stats = [
            'total_products' => Inventory::count(),
            'low_stock_count' => Inventory::stockBajo()->count(),
            'no_stock_count' => Inventory::sinStock()->count(),
            'stock_critico_count' => Inventory::stockCritico()->count(),
            'total_value' => Inventory::with('product')->get()->sum(function($item) {
                $precio = $item->precio_venta ?? $item->product->precio_venta ?? 0;
                return $item->cantidad_actual * $precio;
            }),
        ];

        return Inertia::render('Inventory/Index', [
            'inventoryItems' => $inventoryItems,
            'categories' => $categories,
            'filters' => [
                'search' => $request->get('search', ''),
                'category' => $request->get('category', ''),
                'stock_bajo' => $request->boolean('stock_bajo'),
                'sin_stock' => $request->boolean('sin_stock'),
                'sort' => $request->get('sort', 'id'),
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
        // Obtener productos que no tienen inventario
        $products = Product::with(['category', 'measurement'])
                          ->doesntHave('inventory')
                          ->get();
        
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
            'producto_id' => 'required|exists:products,id|unique:inventory,producto_id',
            'cantidad_actual' => 'required|numeric|min:0',
            'cantidad_minima' => 'required|numeric|min:0',
            'cantidad_maxima' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
        ], [
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
            'producto_id.unique' => 'Este producto ya tiene inventario registrado.',
            'cantidad_actual.required' => 'La cantidad actual es obligatoria.',
            'cantidad_actual.numeric' => 'La cantidad actual debe ser un número.',
            'cantidad_actual.min' => 'La cantidad actual debe ser mayor o igual a 0.',
            'cantidad_minima.required' => 'La cantidad mínima es obligatoria.',
            'cantidad_minima.numeric' => 'La cantidad mínima debe ser un número.',
            'cantidad_minima.min' => 'La cantidad mínima debe ser mayor o igual a 0.',
            'cantidad_maxima.numeric' => 'La cantidad máxima debe ser un número.',
            'cantidad_maxima.min' => 'La cantidad máxima debe ser mayor o igual a 0.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'precio_venta.min' => 'El precio de venta debe ser mayor o igual a 0.',
        ]);

        Inventory::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load(['product.category', 'product.measurement', 'product.supplier']);
        
        return Inertia::render('Inventory/Show', [
            'inventory' => $inventory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $inventory->load(['product.category', 'product.measurement']);
        
        return Inertia::render('Inventory/Edit', [
            'inventory' => $inventory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'cantidad_actual' => 'required|numeric|min:0',
            'cantidad_minima' => 'required|numeric|min:0',
            'cantidad_maxima' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
        ], [
            'cantidad_actual.required' => 'La cantidad actual es obligatoria.',
            'cantidad_actual.numeric' => 'La cantidad actual debe ser un número.',
            'cantidad_actual.min' => 'La cantidad actual debe ser mayor o igual a 0.',
            'cantidad_minima.required' => 'La cantidad mínima es obligatoria.',
            'cantidad_minima.numeric' => 'La cantidad mínima debe ser un número.',
            'cantidad_minima.min' => 'La cantidad mínima debe ser mayor o igual a 0.',
            'cantidad_maxima.numeric' => 'La cantidad máxima debe ser un número.',
            'cantidad_maxima.min' => 'La cantidad máxima debe ser mayor o igual a 0.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'precio_venta.min' => 'El precio de venta debe ser mayor o igual a 0.',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventario eliminado exitosamente.');
    }

    /**
     * Update stock for a specific product (AJAX endpoint)
     */
    public function updateStock(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'cantidad_actual' => 'required|numeric|min:0',
        ], [
            'cantidad_actual.required' => 'La cantidad es obligatoria.',
            'cantidad_actual.numeric' => 'La cantidad debe ser un número.',
            'cantidad_actual.min' => 'La cantidad debe ser mayor o igual a 0.',
        ]);

        $inventory->ajustarStock($validated['cantidad_actual']);

        return back()->with('success', 'Stock actualizado correctamente.');
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts()
    {
        $lowStockProducts = Inventory::with(['product.category'])
            ->stockBajo()
            ->get();

        return response()->json($lowStockProducts);
    }

    /**
     * Get critical stock alerts
     */
    public function criticalStockAlerts()
    {
        $criticalStockProducts = Inventory::with(['product.category'])
            ->stockCritico()
            ->get();

        return response()->json($criticalStockProducts);
    }

    /**
     * Bulk update stock levels
     */
    public function bulkUpdateStock(Request $request)
    {
        $validated = $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:inventory,id',
            'updates.*.cantidad_actual' => 'required|numeric|min:0',
        ]);

        foreach ($validated['updates'] as $update) {
            $inventory = Inventory::find($update['id']);
            $inventory->ajustarStock($update['cantidad_actual']);
        }

        return back()->with('success', 'Stock actualizado en lote exitosamente.');
    }
}