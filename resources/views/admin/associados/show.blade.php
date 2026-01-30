@extends('admin.layouts.app')

@section('title', $associado->nome)
@section('page-title', 'Detalhes do Associado')

@section('header-actions')
    <a href="{{ route('admin.associados.index') }}" class="btn btn--secondary">← Voltar</a>
    <a href="{{ route('admin.associados.edit', $associado) }}" class="btn btn--primary">Editar</a>
@endsection

@section('content')
    <div class="card">
        <div class="card__body">
            <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                @if($associado->foto)
                <div>
                    <img src="{{ Storage::url($associado->foto) }}" alt="{{ $associado->nome }}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 12px;">
                </div>
                @endif
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 24px; margin-bottom: 4px;">{{ $associado->nome }}</h2>
                    <p style="color: var(--gray-500); margin-bottom: 16px;">{{ $associado->cargo }}</p>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        @if($associado->oab)
                        <div>
                            <strong style="font-size: 12px; color: var(--gray-500);">OAB</strong>
                            <p>{{ $associado->oab }}</p>
                        </div>
                        @endif
                        @if($associado->email)
                        <div>
                            <strong style="font-size: 12px; color: var(--gray-500);">E-mail</strong>
                            <p><a href="mailto:{{ $associado->email }}">{{ $associado->email }}</a></p>
                        </div>
                        @endif
                        @if($associado->telefone)
                        <div>
                            <strong style="font-size: 12px; color: var(--gray-500);">Telefone</strong>
                            <p>{{ $associado->telefone }}</p>
                        </div>
                        @endif
                        @if($associado->linkedin)
                        <div>
                            <strong style="font-size: 12px; color: var(--gray-500);">LinkedIn</strong>
                            <p><a href="{{ $associado->linkedin }}" target="_blank">Ver perfil</a></p>
                        </div>
                        @endif
                        <div>
                            <strong style="font-size: 12px; color: var(--gray-500);">Status</strong>
                            <p>
                                <span class="badge {{ $associado->is_active ? 'badge--success' : 'badge--gray' }}">
                                    {{ $associado->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <strong style="font-size: 12px; color: var(--gray-500);">Ordem</strong>
                            <p>{{ $associado->ordem }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($associado->areas_atuacao && count($associado->areas_atuacao) > 0)
            <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--gray-200);">
                <strong style="font-size: 12px; color: var(--gray-500); display: block; margin-bottom: 8px;">Áreas de Atuação</strong>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    @foreach($associado->areas_atuacao as $area)
                        <span class="badge badge--info">{{ $area }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($associado->bio)
            <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--gray-200);">
                <strong style="font-size: 12px; color: var(--gray-500); display: block; margin-bottom: 8px;">Biografia</strong>
                <p style="line-height: 1.7;">{{ $associado->bio }}</p>
            </div>
            @endif
        </div>
    </div>
@endsection
