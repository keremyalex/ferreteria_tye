<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.suppliers')->only(['index', 'show']);
        $this->middleware('can:create.suppliers')->only(['create', 'store']);
        $this->middleware('can:edit.suppliers')->only(['edit', 'update']);
        $this->middleware('can:delete.suppliers')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Búsqueda por NIT, nombre de empresa o persona de contacto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nit', 'ILIKE', "%{$search}%")
                  ->orWhere('nombre_empresa', 'ILIKE', "%{$search}%")
                  ->orWhere('nombre_persona', 'ILIKE', "%{$search}%");
            });
        }

        // Filtro por NIT
        if ($request->filled('nit')) {
            $query->where('nit', 'ILIKE', "%{$request->nit}%");
        }

        // Filtro por nombre de empresa
        if ($request->filled('empresa')) {
            $query->where('nombre_empresa', 'ILIKE', "%{$request->empresa}%");
        }

        // Filtro por contacto
        if ($request->filled('contacto')) {
            $query->where('nombre_persona', 'ILIKE', "%{$request->contacto}%");
        }

        $suppliers = $query->orderBy('nombre_empresa')
                          ->paginate(10)
                          ->appends($request->query());

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $request->only(['search', 'nit', 'empresa', 'contacto'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Suppliers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nit' => 'required|string|max:20|unique:suppliers,nit',
            'nombre_empresa' => 'required|string|max:255',
            'nombre_persona' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255|unique:suppliers,correo',
        ], [
            'nit.required' => 'El NIT es obligatorio.',
            'nit.unique' => 'Ya existe un proveedor con este NIT.',
            'nombre_empresa.required' => 'El nombre de la empresa es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'correo.unique' => 'Ya existe un proveedor con este correo.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'direccion.max' => 'La dirección no puede tener más de 500 caracteres.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nit' => 'required|string|max:20|unique:suppliers,nit,' . $supplier->id,
            'nombre_empresa' => 'required|string|max:255',
            'nombre_persona' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255|unique:suppliers,correo,' . $supplier->id,
        ], [
            'nit.required' => 'El NIT es obligatorio.',
            'nit.unique' => 'Ya existe un proveedor con este NIT.',
            'nombre_empresa.required' => 'El nombre de la empresa es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'correo.unique' => 'Ya existe un proveedor con este correo.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'direccion.max' => 'La dirección no puede tener más de 500 caracteres.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Verificar si el proveedor tiene productos asociados
        if ($supplier->products()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene productos asociados.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}
