<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Associado;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssociadoController extends Controller
{
    /**
     * Public listing for website
     */
    public function publicList()
    {
        $associados = Associado::where('is_active', true)
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get(['id', 'nome', 'cargo', 'oab', 'foto as foto_url', 'bio', 'email', 'linkedin']);

        return response()->json($associados);
    }

    /**
     * List all associados (admin)
     */
    public function index(Request $request)
    {
        $query = Associado::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('oab', 'like', "%{$search}%");
            });
        }

        if ($request->has('ativo')) {
            $query->where('ativo', $request->ativo === 'true');
        }

        $associados = $query->orderBy('ordem')->orderBy('nome')->paginate(15);

        return response()->json($associados);
    }

    /**
     * Store new associado
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:associados,email',
            'cargo' => 'required|string|max:255',
            'especialidade' => 'nullable|string|max:255',
            'oab' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'ordem' => 'nullable|integer',
            'ativo' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('associados', 'public');
        }

        $validated['ativo'] = $validated['ativo'] ?? true;
        $validated['ordem'] = $validated['ordem'] ?? 0;

        $associado = Associado::create($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_associado',
            'description' => "Criou associado: {$associado->nome}",
            'model_type' => Associado::class,
            'model_id' => $associado->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json($associado, 201);
    }

    /**
     * Show single associado
     */
    public function show(Associado $associado)
    {
        return response()->json($associado);
    }

    /**
     * Update associado
     */
    public function update(Request $request, Associado $associado)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:associados,email,' . $associado->id,
            'cargo' => 'required|string|max:255',
            'especialidade' => 'nullable|string|max:255',
            'oab' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'ordem' => 'nullable|integer',
            'ativo' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($associado->foto) {
                Storage::disk('public')->delete($associado->foto);
            }
            $validated['foto'] = $request->file('foto')->store('associados', 'public');
        }

        $associado->update($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_associado',
            'description' => "Atualizou associado: {$associado->nome}",
            'model_type' => Associado::class,
            'model_id' => $associado->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json($associado);
    }

    /**
     * Delete associado
     */
    public function destroy(Request $request, Associado $associado)
    {
        $nome = $associado->nome;

        if ($associado->foto) {
            Storage::disk('public')->delete($associado->foto);
        }

        $associado->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_associado',
            'description' => "Removeu associado: {$nome}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Associado removido com sucesso']);
    }
}
