@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $stats['total_users'] }}</span>
                <span class="stat-label">Usuários</span>
            </div>
            <div class="stat-breakdown">
                <span>{{ $stats['total_admins'] }} admins</span>
                <span>{{ $stats['total_editors'] }} editores</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $stats['total_associados'] }}</span>
                <span class="stat-label">Associados</span>
            </div>
            <div class="stat-breakdown">
                <span>{{ $stats['active_associados'] }} ativos</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon--purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14,2 14,8 20,8"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $stats['total_posts'] }}</span>
                <span class="stat-label">Posts do Blog</span>
            </div>
            <div class="stat-breakdown">
                <span>{{ $stats['published_posts'] }} publicados</span>
                <span>{{ $stats['draft_posts'] }} rascunhos</span>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-panels">
        <div class="panel">
            <div class="panel__header">
                <h2 class="panel__title">Atividade Recente</h2>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.logs.index') }}" class="panel__link">Ver todos</a>
                @endif
            </div>
            <div class="panel__body">
                @if($recentLogs->isEmpty())
                    <p class="empty-message">Nenhuma atividade registrada.</p>
                @else
                    <ul class="activity-list">
                        @foreach($recentLogs as $log)
                        <li class="activity-item">
                            <div class="activity-icon activity-icon--{{ $log->action }}">
                                @if($log->action === 'login')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                        <polyline points="10 17 15 12 10 7"></polyline>
                                        <line x1="15" y1="12" x2="3" y2="12"></line>
                                    </svg>
                                @elseif($log->action === 'logout')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                @endif
                            </div>
                            <div class="activity-content">
                                <span class="activity-user">{{ $log->user?->name ?? 'Sistema' }}</span>
                                <span class="activity-description">{{ $log->description }}</span>
                                <span class="activity-time">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="panel">
            <div class="panel__header">
                <h2 class="panel__title">Posts Recentes</h2>
                <a href="{{ route('admin.posts.index') }}" class="panel__link">Ver todos</a>
            </div>
            <div class="panel__body">
                @if($recentPosts->isEmpty())
                    <p class="empty-message">Nenhum post encontrado.</p>
                @else
                    <ul class="posts-list">
                        @foreach($recentPosts as $post)
                        <li class="post-item">
                            <div class="post-info">
                                <a href="{{ route('admin.posts.show', $post) }}" class="post-title">{{ $post->titulo }}</a>
                                <span class="post-meta">
                                    por {{ $post->user?->name ?? 'Desconhecido' }} • {{ $post->created_at->format('d/m/Y') }}
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
</div>
@endsection
