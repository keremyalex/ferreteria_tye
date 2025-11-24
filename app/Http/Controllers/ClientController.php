<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.clients')->only(['index', 'show']);
        $this->middleware('can:create.clients')->only(['create', 'store']);
        $this->middleware('can:edit.clients')->only(['edit', 'update']);
        $this->middleware('can:delete.clients')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Búsqueda general por nombre, CI, NIT o teléfono
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('ci', 'ILIKE', "%{$search}%")
                  ->orWhere('nit', 'ILIKE', "%{$search}%")
                  ->orWhere('telf', 'ILIKE', "%{$search}%");
            });
        }

        // Filtro por nombre
        if ($request->filled('nombre')) {
            $query->byName($request->nombre);
        }

        // Filtro por CI
        if ($request->filled('ci')) {
            $query->byCi($request->ci);
        }

        // Filtro por NIT
        if ($request->filled('nit')) {
            $query->byNit($request->nit);
        }

        // Filtro por teléfono
        if ($request->filled('telefono')) {
            $query->byPhone($request->telefono);
        }

        $clients = $query->orderBy('nombre')
                        ->paginate(10)
                        ->appends($request->query());

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search', 'nombre', 'ci', 'nit', 'telefono'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'nullable|string|max:20|unique:clients,nit',
            'ci' => 'nullable|string|max:20|unique:clients,ci',
            'telf' => 'nullable|string|max:20',
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nit.unique' => 'Ya existe un cliente con este NIT.',
            'nit.max' => 'El NIT no puede tener más de 20 caracteres.',
            'ci.unique' => 'Ya existe un cliente con este CI.',
            'ci.max' => 'El CI no puede tener más de 20 caracteres.',
            'telf.max' => 'El teléfono no puede tener más de 20 caracteres.',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // $client->loadCount('sales'); // Cuando se implemente el modelo Sale
        
        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'nullable|string|max:20|unique:clients,nit,' . $client->id,
            'ci' => 'nullable|string|max:20|unique:clients,ci,' . $client->id,
            'telf' => 'nullable|string|max:20',
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nit.unique' => 'Ya existe un cliente con este NIT.',
            'nit.max' => 'El NIT no puede tener más de 20 caracteres.',
            'ci.unique' => 'Ya existe un cliente con este CI.',
            'ci.max' => 'El CI no puede tener más de 20 caracteres.',
            'telf.max' => 'El teléfono no puede tener más de 20 caracteres.',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // Verificar si el cliente tiene ventas asociadas
        // if ($client->sales()->count() > 0) {
        //     return back()->with('error', 'No se puede eliminar el cliente porque tiene ventas asociadas.');
        // }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
}