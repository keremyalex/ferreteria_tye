<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.categories')->only(['index', 'show']);
        $this->middleware('can:create.categories')->only(['create', 'store']);
        $this->middleware('can:edit.categories')->only(['edit', 'update']);
        $this->middleware('can:delete.categories')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Filtrar por estado
        if ($request->filled('status')) {
            $estado = $request->status === 'active' ? 'activo' : 'inactivo';
            $query->where('estado', $estado);
        }

        // Ordenar resultados
        $sortField = $request->get('sort', 'nombre');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'products_count') {
            $query->orderBy('products_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $categories = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search', 'status', 'sort'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categories,nombre',
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con este nombre.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->loadCount('products');
        $category->load(['products' => function ($query) {
            $query->with(['supplier:id,nombre_empresa', 'measurement:id,nombre,simbolo'])
                  ->select('id', 'nombre', 'descripcion', 'precio', 'imagen', 'category_id', 'supplier_id', 'measurement_id')
                  ->take(10); // Limitar a 10 productos para mejor performance
        }]);
        
        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categories,nombre,' . $category->id,
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con este nombre.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}
