<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Measurement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.products')->only(['index', 'show']);
        $this->middleware('can:create.products')->only(['create', 'store']);
        $this->middleware('can:edit.products')->only(['edit', 'update']);
        $this->middleware('can:delete.products')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'measurement', 'supplier']);

        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%');
        }

        // Filtrar por categoría
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtrar por proveedor
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        // Ordenar resultados
        $sortField = $request->get('sort', 'nombre');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate(10)->withQueryString();
        
        // Obtener categorías y proveedores para los filtros
        $categories = \App\Models\Category::all();
        $suppliers = \App\Models\Supplier::all();
        
        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'filters' => $request->only(['search', 'category', 'supplier', 'sort'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Products/Create', [
            'categories' => Category::all(),
            'measurements' => Measurement::all(),
            'suppliers' => Supplier::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'precio_venta' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'measurement_id' => 'required|exists:measurements,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, webp.',
            'imagen.max' => 'La imagen no debe ser mayor a 2MB.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'precio_venta.min' => 'El precio de venta debe ser mayor o igual a 0.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'measurement_id.required' => 'La unidad de medida es obligatoria.',
            'measurement_id.exists' => 'La unidad de medida seleccionada no existe.',
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',
        ]);

        // Manejar la imagen si se proporciona
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $filename = $this->generateImageFilename($imagen);
            
            // Guardar imagen
            $path = $imagen->storeAs('products', $filename, 'public');
            $validated['imagen'] = $path;
        } else {
            unset($validated['imagen']);
        }
        
        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'measurement', 'supplier', 'inventory']);
        
        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => Category::all(),
            'measurements' => Measurement::all(),
            'suppliers' => Supplier::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'precio_venta' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'measurement_id' => 'required|exists:measurements,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, webp.',
            'imagen.max' => 'La imagen no debe ser mayor a 2MB.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'precio_venta.min' => 'El precio de venta debe ser mayor o igual a 0.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'measurement_id.required' => 'La unidad de medida es obligatoria.',
            'measurement_id.exists' => 'La unidad de medida seleccionada no existe.',
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',
        ]);

        // Manejar la imagen si se proporciona una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe y es un archivo local
            if ($product->imagen && !str_starts_with($product->imagen, 'http') && Storage::disk('public')->exists($product->imagen)) {
                Storage::disk('public')->delete($product->imagen);
            }

            $imagen = $request->file('imagen');
            $filename = $this->generateImageFilename($imagen);
            
            // Guardar nueva imagen
            $path = $imagen->storeAs('products', $filename, 'public');
            $validated['imagen'] = $path;
        } else {
            // Mantener la imagen actual si no se subió una nueva
            unset($validated['imagen']);
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Verificar si el producto tiene inventario
        if ($product->inventories()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el producto porque tiene registros de inventario.');
        }

        // Eliminar imagen si existe y es un archivo local
        if ($product->imagen && !str_starts_with($product->imagen, 'http') && Storage::disk('public')->exists($product->imagen)) {
            Storage::disk('public')->delete($product->imagen);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * Generar nombre único para la imagen
     */
    private function generateImageFilename($file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        
        // Asegurar que el nombre sea único
        while (Storage::disk('public')->exists('products/' . $filename)) {
            $filename = Str::random(40) . '.' . $extension;
        }
        
        return $filename;
    }
}
