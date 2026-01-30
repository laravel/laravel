@extends('admin.layouts.app')

@section('title', 'Usuários')
@section('page-title', 'Usuários')

@section('header-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn--primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Novo Usuário
    </a>
@endsection

@section('content')
    <form action="{{ route('admin.users.index') }}" method="GET" class="filter-bar">
        <input type="text" name="search" class="form-input" placeholder="Buscar por nome ou email..." value="{{ request('search') }}">
        <select name="role" class="form-input form-select">
            <option value="">Todos os perfis</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="editor" {{ request('role') == 'editor' ? 'selected' : '' }}>Editor</option>
            <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Cliente</option>
        </select>
        <button type="submit" class="btn btn--primary">Filtrar</button>
        @if(request()->hasAny(['search', 'role']))
            <a href="{{ route('admin.users.index') }}" class="btn btn--secondary">Limpar</a>
        @endif
    </form>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'badge--danger' : ($user->role === 'editor' ? 'badge--info' : 'badge--gray') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'badge--success' : 'badge--gray' }}">
                            {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.users.edit', $user) }}" class="table-action table-action--edit" title="Editar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline" onsubmit="return confirm('Tem certeza que deseja remover este usuário?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="table-action table-action--delete" title="Remover">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-message">Nenhum usuário encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->withQueryString()->links() }}
@endsection
