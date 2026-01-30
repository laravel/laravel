@extends('editor.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

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
<div class="dashboard">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon--purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14,2 14,8 20,8"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $stats['total_posts'] }}</span>
                <span class="stat-label">Meus Artigos</span>
            </div>
            <div class="stat-breakdown">
                <span>{{ $stats['published_posts'] }} publicados</span>
                <span>{{ $stats['draft_posts'] }} rascunhos</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ number_format($stats['total_views']) }}</span>
                <span class="stat-label">Visualizações</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $stats['published_posts'] }}</span>
                <span class="stat-label">Publicados</span>
            </div>
        </div>
    </div>

    <!-- Recent Posts -->
    <div class="panel">
        <div class="panel__header">
            <h2 class="panel__title">Artigos Recentes</h2>
            <a href="{{ route('editor.posts.index') }}" class="panel__link">Ver todos</a>
        </div>
        <div class="panel__body">
            @if($recentPosts->isEmpty())
                <div class="empty-message">
                    <p>Você ainda não criou nenhum artigo.</p>
                    <a href="{{ route('editor.posts.create') }}" class="btn btn--primary btn--small" style="margin-top: 12px;">Criar primeiro artigo</a>
                </div>
            @else
                <ul class="posts-list">
                    @foreach($recentPosts as $post)
                    <li class="post-item">
                        <div class="post-info">
                            <a href="{{ route('editor.posts.edit', $post) }}" class="post-title">{{ $post->titulo }}</a>
                            <span class="post-meta">
                                Atualizado {{ $post->updated_at->diffForHumans() }} • {{ number_format($post->views) }} views
                            </span>
                        </div>
                        <span class="post-status post-status--{{ $post->status }}">
                            {{ ucfirst($post->status) }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
