@extends('admin.layouts.app')

@section('title', $post->titulo)
@section('page-title', 'Detalhes do Post')

@section('header-actions')
    <a href="{{ route('admin.posts.index') }}" class="btn btn--secondary">← Voltar</a>
    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn--primary">Editar</a>
    @if($post->status !== 'publicado')
    <form action="{{ route('admin.posts.publish', $post) }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn--success">Publicar</button>
    </form>
    @endif
    @if($post->status === 'publicado')
    <form action="{{ route('admin.posts.archive', $post) }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn--danger">Arquivar</button>
    </form>
    @endif
@endsection

@section('content')
    <div class="card">
        <div class="card__body">
            <div style="margin-bottom: 20px;">
                <span class="post-status post-status--{{ $post->status }}">{{ ucfirst($post->status) }}</span>
                @if($post->is_featured)
                    <span class="badge badge--warning" style="margin-left: 8px;">Destaque</span>
                @endif
            </div>

            <h1 style="font-size: 28px; margin-bottom: 8px;">{{ $post->titulo }}</h1>
            
            <p style="color: var(--gray-500); margin-bottom: 24px;">
                Por <strong>{{ $post->user?->name ?? 'Desconhecido' }}</strong> 
                • Criado em {{ $post->created_at->format('d/m/Y H:i') }}
                @if($post->published_at)
                    • Publicado em {{ $post->published_at->format('d/m/Y H:i') }}
                @endif
                • {{ number_format($post->views) }} views
            </p>

            <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--gray-200);">
                @if($post->categoria)
                <div>
                    <strong style="font-size: 12px; color: var(--gray-500);">Categoria</strong>
                    <p>{{ $post->categoria }}</p>
                </div>
                @endif
                <div>
                    <strong style="font-size: 12px; color: var(--gray-500);">Slug</strong>
                    <p><code>{{ $post->slug }}</code></p>
                </div>
            </div>

            @if($post->resumo)
            <div style="margin-bottom: 24px;">
                <strong style="font-size: 12px; color: var(--gray-500); display: block; margin-bottom: 8px;">Resumo</strong>
                <p style="font-size: 18px; color: var(--gray-600); font-style: italic;">{{ $post->resumo }}</p>
            </div>
            @endif

            @if($post->tags && count($post->tags) > 0)
            <div style="margin-bottom: 24px;">
                <strong style="font-size: 12px; color: var(--gray-500); display: block; margin-bottom: 8px;">Tags</strong>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    @foreach($post->tags as $tag)
                        <span class="badge badge--gray">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div style="padding-top: 24px; border-top: 1px solid var(--gray-200);">
                <strong style="font-size: 12px; color: var(--gray-500); display: block; margin-bottom: 16px;">Conteúdo</strong>
                <div style="line-height: 1.8; color: var(--gray-700);">
                    {!! nl2br(e($post->conteudo)) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
