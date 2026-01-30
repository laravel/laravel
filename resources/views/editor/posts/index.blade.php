@extends('editor.layouts.app')

@section('title', 'Meus Artigos')
@section('page-title', 'Meus Artigos')

@section('header-actions')
    <a href="{{ route('editor.posts.create') }}" class="btn btn--primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Novo Artigo
    </a>
@endsection

@section('content')
    <form action="{{ route('editor.posts.index') }}" method="GET" class="filter-bar">
        <input type="text" name="search" class="form-input" placeholder="Buscar por título..." value="{{ request('search') }}">
        <select name="status" class="form-input form-select">
            <option value="">Todos os status</option>
            <option value="publicado" {{ request('status') == 'publicado' ? 'selected' : '' }}>Publicado</option>
            <option value="rascunho" {{ request('status') == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
            <option value="arquivado" {{ request('status') == 'arquivado' ? 'selected' : '' }}>Arquivado</option>
        </select>
        <button type="submit" class="btn btn--primary">Filtrar</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('editor.posts.index') }}" class="btn btn--secondary">Limpar</a>
        @endif
    </form>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Atualizado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>
                        <strong>{{ Str::limit($post->titulo, 50) }}</strong>
                        @if($post->is_featured)
                            <span class="badge badge--warning" style="margin-left: 8px;">Destaque</span>
                        @endif
                    </td>
                    <td>
                        <span class="post-status post-status--{{ $post->status }}">{{ ucfirst($post->status) }}</span>
                    </td>
                    <td>{{ number_format($post->views) }}</td>
                    <td>{{ $post->updated_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('editor.posts.show', $post) }}" class="table-action table-action--view" title="Ver">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                            <a href="{{ route('editor.posts.edit', $post) }}" class="table-action table-action--edit" title="Editar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('editor.posts.destroy', $post) }}" method="POST" style="display:inline" onsubmit="return confirm('Tem certeza que deseja remover este artigo?')">
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
                    <td colspan="5" class="empty-message">
                        Você ainda não criou nenhum artigo.
                        <a href="{{ route('editor.posts.create') }}" style="display: block; margin-top: 8px;">Criar primeiro artigo →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $posts->withQueryString()->links() }}
@endsection
