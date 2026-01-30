<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Associado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssociadoController extends Controller
{
    /**
     * Display a listing of associados.
     */
    public function index(Request $request)
    {
        $query = Associado::query();

        // Filter by search
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cargo', 'like', "%{$search}%")
                  ->orWhere('oab', 'like', "%{$search}%");
            });
        }

        // Filter by cargo
        if ($cargo = $request->get('cargo')) {
            $query->where('cargo', $cargo);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $associados = $query->ordered()->paginate(15);

        $cargos = Associado::distinct()->pluck('cargo')->filter();

        return view('admin.associados.index', compact('associados', 'cargos'));
    }

    /**
     * Show the form for creating a new associado.
     */
    public function create()
    {
        return view('admin.associados.create');
    }

    /**
     * Store a newly created associado.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:100',
            'oab' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'linkedin' => 'nullable|url|max:255',
            'areas_atuacao' => 'nullable|string',
            'ordem' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Handle areas_atuacao as JSON array
        if (!empty($validated['areas_atuacao'])) {
            $validated['areas_atuacao'] = array_map('trim', explode(',', $validated['areas_atuacao']));
        }

        // Handle photo upload
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('associados', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $associado = Associado::create($validated);

        ActivityLog::log('create', "Criou associado: {$associado->nome}", $associado);

        return redirect()
            ->route('admin.associados.index')
            ->with('success', 'Associado criado com sucesso!');
    }

    /**
     * Display the specified associado.
     */
    public function show(Associado $associado)
    {
        return view('admin.associados.show', compact('associado'));
    }

    /**
     * Show the form for editing the specified associado.
     */
    public function edit(Associado $associado)
    {
        return view('admin.associados.edit', compact('associado'));
    }

    /**
     * Update the specified associado.
     */
    public function update(Request $request, Associado $associado)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:100',
            'oab' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'linkedin' => 'nullable|url|max:255',
            'areas_atuacao' => 'nullable|string',
            'ordem' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'foto' => 'nullable|image|max:2048',
        ]);

        $oldValues = $associado->toArray();

        // Handle areas_atuacao as JSON array
        if (!empty($validated['areas_atuacao'])) {
            $validated['areas_atuacao'] = array_map('trim', explode(',', $validated['areas_atuacao']));
        } else {
            $validated['areas_atuacao'] = null;
        }

        // Handle photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($associado->foto) {
                Storage::disk('public')->delete($associado->foto);
            }
            $validated['foto'] = $request->file('foto')->store('associados', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $associado->update($validated);

        ActivityLog::log('update', "Atualizou associado: {$associado->nome}", $associado, $oldValues, $associado->toArray());

        return redirect()
            ->route('admin.associados.index')
            ->with('success', 'Associado atualizado com sucesso!');
    }

    /**
     * Remove the specified associado.
     */
    public function destroy(Associado $associado)
    {
        $nome = $associado->nome;
        
        // Delete photo
        if ($associado->foto) {
            Storage::disk('public')->delete($associado->foto);
        }

        $associado->delete();

        ActivityLog::log('delete', "Removeu associado: {$nome}");

        return redirect()
            ->route('admin.associados.index')
            ->with('success', 'Associado removido com sucesso!');
    }
}
