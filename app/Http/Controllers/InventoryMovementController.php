<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\InventoryMovementDetail;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class InventoryMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.inventory')->only(['index', 'show']);
        $this->middleware('can:create.inventory')->only(['create', 'store']);
        $this->middleware('can:edit.inventory')->only(['edit', 'update', 'apply', 'revert']);
        $this->middleware('can:delete.inventory')->only(['destroy']);
    }

    /**
     * Mostrar listado de movimientos
     */
    public function index(Request $request)
    {
        $query = InventoryMovement::with(['user', 'details.product.category']);

        // Filtros
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('referencia', 'like', '%' . $request->search . '%')
                  ->orWhere('observaciones', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'fecha');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $movements = $query->paginate(15)->withQueryString();

        // Estadísticas rápidas
        $stats = [
            'total_movimientos' => InventoryMovement::count(),
            'entradas_mes' => InventoryMovement::entradas()
                ->whereMonth('fecha', Carbon::now()->month)
                ->whereYear('fecha', Carbon::now()->year)
                ->count(),
            'salidas_mes' => InventoryMovement::salidas()
                ->whereMonth('fecha', Carbon::now()->month)
                ->whereYear('fecha', Carbon::now()->year)
                ->count(),
            'pendientes' => InventoryMovement::where('estado', 'pendiente')->count(),
        ];

        return Inertia::render('Inventory/Movements/Index', [
            'movements' => $movements,
            'stats' => $stats,
            'filters' => $request->only(['tipo', 'estado', 'search', 'fecha_desde', 'fecha_hasta', 'sort', 'direction'])
        ]);
    }

    /**
     * Mostrar formulario para crear entrada de mercancía
     */
    public function createEntrada()
    {
        $products = Product::with(['category', 'measurement', 'inventory'])
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Inventory/Movements/CreateEntrada', [
            'products' => $products
        ]);
    }

    /**
     * Mostrar formulario para crear salida de mercancía
     */
    public function createSalida()
    {
        $products = Product::with(['category', 'measurement', 'inventory'])
            ->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            })
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Inventory/Movements/CreateSalida', [
            'products' => $products
        ]);
    }

    /**
     * Mostrar formulario para crear ajuste de inventario
     */
    public function createAjuste()
    {
        $products = Product::with(['category', 'measurement', 'inventory'])
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Inventory/Movements/CreateAjuste', [
            'products' => $products
        ]);
    }

    /**
     * Procesar entrada de mercancía
     */
    public function storeEntrada(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'referencia' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:pendiente,aplicado',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:products,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.lote' => 'nullable|string|max:255',
            'productos.*.observaciones' => 'nullable|string',
        ]);

        return $this->createMovement('entrada', $validated);
    }

    /**
     * Procesar salida de mercancía
     */
    public function storeSalida(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'referencia' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:pendiente,aplicado',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:products,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.observaciones' => 'nullable|string',
        ]);

        // Validar stock disponible para salidas
        foreach ($validated['productos'] as $producto) {
            $inventory = Inventory::where('producto_id', $producto['producto_id'])->first();
            if (!$inventory || $inventory->cantidad_actual < $producto['cantidad']) {
                $productName = Product::find($producto['producto_id'])->nombre;
                return back()->withErrors([
                    'productos' => "Stock insuficiente para el producto: {$productName}. Stock disponible: " . ($inventory->cantidad_actual ?? 0)
                ]);
            }
        }

        return $this->createMovement('salida', $validated);
    }

    /**
     * Procesar ajuste de inventario
     */
    public function storeAjuste(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'referencia' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:aplicado',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:products,id',
            'productos.*.stock_real' => 'required|numeric|min:0',
            'productos.*.cantidad' => 'required|numeric', // Permitir valores negativos
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.observaciones' => 'nullable|string',
        ]);

        // Convertir ajustes a formato de movimientos
        $productosMovimiento = [];
        foreach ($validated['productos'] as $producto) {
            $inventory = Inventory::where('producto_id', $producto['producto_id'])->first();
            $cantidadActual = $inventory ? $inventory->cantidad_actual : 0;
            $diferencia = $producto['stock_real'] - $cantidadActual;

            if ($diferencia != 0) {
                $productosMovimiento[] = [
                    'producto_id' => $producto['producto_id'],
                    'cantidad' => $diferencia, // Usar la diferencia calculada (puede ser negativa)
                    'precio_unitario' => $producto['precio_unitario'],
                    'observaciones' => $producto['observaciones'] ?? "Ajuste: {$cantidadActual} → {$producto['stock_real']}",
                ];
            }
        }

        $validated['productos'] = $productosMovimiento;
        return $this->createMovement('ajuste', $validated);
    }

    /**
     * Crear movimiento genérico
     */
    private function createMovement(string $tipo, array $validated)
    {
        return DB::transaction(function () use ($tipo, $validated) {
            // Crear el movimiento principal
            $movement = InventoryMovement::create([
                'tipo' => $tipo,
                'fecha' => $validated['fecha'],
                'referencia' => $validated['referencia'],
                'observaciones' => $validated['observaciones'],
                'estado' => 'pendiente', // Siempre crear como pendiente primero
                'usuario_id' => Auth::id(),
            ]);

            // Crear los detalles
            foreach ($validated['productos'] as $producto) {
                InventoryMovementDetail::create([
                    'movimiento_id' => $movement->id,
                    'producto_id' => $producto['producto_id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'lote' => $producto['lote'] ?? null,
                    'observaciones' => $producto['observaciones'] ?? null,
                ]);
            }

            // Aplicar automáticamente si el estado es 'aplicado'
            if ($validated['estado'] === 'aplicado') {
                $movement->aplicar();
            }

            return redirect()->route('inventory.movements.show', $movement)
                ->with('success', ucfirst($tipo) . ' procesada exitosamente.');
        });
    }

    /**
     * Mostrar detalle de un movimiento
     */
    public function show(InventoryMovement $movement)
    {
        $movement->load(['user', 'details.product.category']);

        return Inertia::render('Inventory/Movements/Show', [
            'movement' => $movement
        ]);
    }

    /**
     * Aplicar movimiento pendiente
     */
    public function apply(InventoryMovement $movement)
    {
        if ($movement->estado !== 'pendiente') {
            return back()->withErrors(['error' => 'Solo se pueden aplicar movimientos pendientes.']);
        }

        try {
            $movement->aplicar();
            return back()->with('success', 'Movimiento aplicado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al aplicar movimiento: ' . $e->getMessage()]);
        }
    }

    /**
     * Revertir movimiento aplicado
     */
    public function revert(InventoryMovement $movement)
    {
        if ($movement->estado !== 'aplicado') {
            return back()->withErrors(['error' => 'Solo se pueden revertir movimientos aplicados.']);
        }

        try {
            $movement->revertir();
            return back()->with('success', 'Movimiento revertido exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al revertir movimiento: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar movimiento
     */
    public function destroy(InventoryMovement $movement)
    {
        if ($movement->estado === 'aplicado') {
            return back()->withErrors(['error' => 'No se puede eliminar un movimiento aplicado. Primero debe revertirlo.']);
        }

        $movement->delete();
        return redirect()->route('inventory.movements.index')
            ->with('success', 'Movimiento eliminado exitosamente.');
    }
}