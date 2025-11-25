<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    /**
     * Mostrar catálogo público de productos
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'measurement', 'supplier', 'inventory'])
            ->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0); // Solo productos con stock
            });

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'ILIKE', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'ILIKE', '%' . $request->search . '%');
            });
        }

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'nombre');
        $sortDirection = $request->get('direction', 'asc');
        
        $validSortFields = ['nombre', 'precio_venta', 'created_at'];
        if (in_array($sortField, $validSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $products = $query->paginate(12)->withQueryString();
        
        // Agregar información de stock a cada producto
        $products->getCollection()->transform(function ($product) {
            $product->stock_disponible = $product->inventory->cantidad_actual ?? 0;
            $product->precio_final = $product->inventory->precio_venta ?? $product->precio_venta;
            return $product;
        });

        $categories = Category::whereHas('products', function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        })->withCount(['products' => function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        }])->get();

        return Inertia::render('Catalog/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'sort', 'direction'])
        ]);
    }

    /**
     * Mostrar detalle de un producto
     */
    public function show(Product $product)
    {
        $product->load(['category', 'measurement', 'supplier', 'inventory']);
        
        // Verificar que el producto tenga stock
        if (!$product->inventory || $product->inventory->cantidad_actual <= 0) {
            abort(404, 'Producto sin stock disponible');
        }

        $product->stock_disponible = $product->inventory->cantidad_actual;
        $product->precio_final = $product->inventory->precio_venta ?? $product->precio_venta;

        // Productos relacionados (misma categoría con stock)
        $relatedProducts = Product::with(['category', 'inventory'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            })
            ->limit(4)
            ->get()
            ->map(function ($relatedProduct) {
                $relatedProduct->stock_disponible = $relatedProduct->inventory->cantidad_actual ?? 0;
                $relatedProduct->precio_final = $relatedProduct->inventory->precio_venta ?? $relatedProduct->precio_venta;
                return $relatedProduct;
            });

        return Inertia::render('Catalog/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }

    /**
     * API para validar productos del carrito
     */
    public function validateCart(Request $request)
    {
        $items = $request->input('items', []);
        $validatedItems = [];

        foreach ($items as $item) {
            $product = Product::with('inventory')->find($item['product_id']);
            
            if (!$product || !$product->inventory) {
                continue;
            }

            $maxQuantity = $product->inventory->cantidad_actual;
            $requestedQuantity = min($item['cantidad'], $maxQuantity);
            
            $validatedItems[] = [
                'product_id' => $product->id,
                'nombre' => $product->nombre,
                'precio' => $product->inventory->precio_venta ?? $product->precio_venta,
                'imagen' => $product->imagen,
                'cantidad' => $requestedQuantity,
                'cantidad_original' => $item['cantidad'],
                'stock_disponible' => $maxQuantity,
                'subtotal' => $requestedQuantity * ($product->inventory->precio_venta ?? $product->precio_venta),
                'stock_insuficiente' => $item['cantidad'] > $maxQuantity
            ];
        }

        return response()->json([
            'items' => $validatedItems,
            'total' => collect($validatedItems)->sum('subtotal')
        ]);
    }
}
