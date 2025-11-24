<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeasurementController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.measurements')->only(['index', 'show']);
        $this->middleware('can:create.measurements')->only(['create', 'store']);
        $this->middleware('can:edit.measurements')->only(['edit', 'update']);
        $this->middleware('can:delete.measurements')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Measurement::withCount('products');

        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('simbolo', 'like', '%' . $request->search . '%');
        }

        // Ordenar resultados
        $sortField = $request->get('sort', 'nombre');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'products_count') {
            $query->orderBy('products_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $measurements = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Measurements/Index', [
            'measurements' => $measurements,
            'filters' => $request->only(['search', 'sort'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Measurements/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:measurements,nombre',
            'simbolo' => 'required|string|max:10|unique:measurements,simbolo',
        ], [
            'nombre.required' => 'El nombre de la unidad de medida es obligatorio.',
            'nombre.unique' => 'Ya existe una unidad de medida con este nombre.',
            'simbolo.required' => 'El símbolo es obligatorio.',
            'simbolo.unique' => 'Ya existe una unidad de medida con este símbolo.',
        ]);

        Measurement::create($validated);

        return redirect()->route('measurements.index')
            ->with('success', 'Unidad de medida creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Measurement $measurement)
    {
        $measurement->loadCount('products');
        $measurement->load(['products' => function ($query) {
            $query->with(['category:id,nombre', 'supplier:id,nombre_empresa'])
                  ->select('id', 'nombre', 'descripcion', 'precio', 'imagen', 'category_id', 'supplier_id', 'measurement_id')
                  ->take(10); // Limitar a 10 productos para mejor performance
        }]);
        
        return Inertia::render('Measurements/Show', [
            'measurement' => $measurement,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Measurement $measurement)
    {
        return Inertia::render('Measurements/Edit', [
            'measurement' => $measurement,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Measurement $measurement)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:measurements,nombre,' . $measurement->id,
            'simbolo' => 'required|string|max:10|unique:measurements,simbolo,' . $measurement->id,
        ], [
            'nombre.required' => 'El nombre de la unidad de medida es obligatorio.',
            'nombre.unique' => 'Ya existe una unidad de medida con este nombre.',
            'simbolo.required' => 'El símbolo es obligatorio.',
            'simbolo.unique' => 'Ya existe una unidad de medida con este símbolo.',
        ]);

        $measurement->update($validated);

        return redirect()->route('measurements.index')
            ->with('success', 'Unidad de medida actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Measurement $measurement)
    {
        // Verificar si la unidad de medida está siendo usada por productos
        if ($measurement->products()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la unidad de medida porque está siendo usada por productos.');
        }

        $measurement->delete();

        return redirect()->route('measurements.index')
            ->with('success', 'Unidad de medida eliminada exitosamente.');
    }
}
