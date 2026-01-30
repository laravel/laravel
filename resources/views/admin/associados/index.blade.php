@extends('admin.layouts.app')

@section('title', 'Associados')
@section('page-title', 'Associados')

@section('header-actions')
    <a href="{{ route('admin.associados.create') }}" class="btn btn--primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Novo Associado
    </a>
@endsection

@section('content')
    <!-- Filter Bar -->
    <form action="{{ route('admin.associados.index') }}" method="GET" class="filter-bar">
        <input 
            type="text" 
            name="search" 
            class="form-input" 
            placeholder="Buscar por nome, cargo ou OAB..."
            value="{{ request('search') }}"
        >
        <select name="cargo" class="form-input form-select">
            <option value="">Todos os cargos</option>
            @foreach($cargos as $cargo)
                <option value="{{ $cargo }}" {{ request('cargo') == $cargo ? 'selected' : '' }}>
                    {{ $cargo }}
                </option>
            @endforeach
        </select>
        <select name="is_active" class="form-input form-select">
            <option value="">Todos os status</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativos</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativos</option>
        </select>
        <button type="submit" class="btn btn--primary">Filtrar</button>
        @if(request()->hasAny(['search', 'cargo', 'is_active']))
            <a href="{{ route('admin.associados.index') }}" class="btn btn--secondary">Limpar</a>
        @endif
    </form>

    <!-- Data Table -->
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>OAB</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Ordem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($associados as $associado)
                <tr>
                    <td>
                        <strong>{{ $associado->nome }}</strong>
                    </td>
                    <td>{{ $associado->cargo }}</td>
                    <td>{{ $associado->oab ?? '-' }}</td>
                    <td>{{ $associado->email ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $associado->is_active ? 'badge--success' : 'badge--gray' }}">
                            {{ $associado->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td>{{ $associado->ordem }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.associados.show', $associado) }}" class="table-action table-action--view" title="Ver">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                            <a href="{{ route('admin.associados.edit', $associado) }}" class="table-action table-action--edit" title="Editar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.associados.destroy', $associado) }}" method="POST" style="display:inline" onsubmit="return confirm('Tem certeza que deseja remover este associado?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="table-action table-action--delete" title="Remover">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-message">Nenhum associado encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $associados->withQueryString()->links() }}
@endsection
