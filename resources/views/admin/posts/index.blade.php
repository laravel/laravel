@extends('admin.layouts.app')

@section('title', 'Blog')
@section('page-title', 'Gestão do Blog')

@section('content')
    <form action="{{ route('admin.posts.index') }}" method="GET" class="filter-bar">
        <input type="text" name="search" class="form-input" placeholder="Buscar por título ou categoria..." value="{{ request('search') }}">
        <select name="status" class="form-input form-select">
            <option value="">Todos os status</option>
            <option value="publicado" {{ request('status') == 'publicado' ? 'selected' : '' }}>Publicado</option>
            <option value="rascunho" {{ request('status') == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
            <option value="arquivado" {{ request('status') == 'arquivado' ? 'selected' : '' }}>Arquivado</option>
        </select>
        <select name="categoria" class="form-input form-select">
            <option value="">Todas as categorias</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria }}" {{ request('categoria') == $categoria ? 'selected' : '' }}>{{ $categoria }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn--primary">Filtrar</button>
        @if(request()->hasAny(['search', 'status', 'categoria']))
            <a href="{{ route('admin.posts.index') }}" class="btn btn--secondary">Limpar</a>
        @endif
    </form>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>
                        <a href="{{ route('admin.posts.show', $post) }}" style="color: var(--gray-800); text-decoration: none;">
                            <strong>{{ Str::limit($post->titulo, 40) }}</strong>
                        </a>
                        @if($post->is_featured)
                            <span class="badge badge--warning" style="margin-left: 8px;">Destaque</span>
                        @endif
                    </td>
                    <td>{{ $post->user?->name ?? 'Desconhecido' }}</td>
                    <td>{{ $post->categoria ?? '-' }}</td>
                    <td>
                        <span class="post-status post-status--{{ $post->status }}">{{ ucfirst($post->status) }}</span>
                    </td>
                    <td>{{ number_format($post->views) }}</td>
                    <td>{{ $post->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.posts.show', $post) }}" class="table-action table-action--view" title="Ver">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="table-action table-action--edit" title="Editar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" style="display:inline" onsubmit="return confirm('Tem certeza que deseja remover este post?')">
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
                    <td colspan="7" class="empty-message">Nenhum post encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $posts->withQueryString()->links() }}
@endsection
