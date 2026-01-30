@extends('admin.layouts.app')

@section('title', 'Editar Associado')
@section('page-title', 'Editar Associado')

@section('header-actions')
    <a href="{{ route('admin.associados.index') }}" class="btn btn--secondary">
        ← Voltar
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Dados do Associado</h2>
        </div>
        <form action="{{ route('admin.associados.update', $associado) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card__body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" id="nome" name="nome" class="form-input @error('nome') form-input--error @enderror" value="{{ old('nome', $associado->nome) }}" required>
                        @error('nome')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="cargo" class="form-label">Cargo *</label>
                        <input type="text" id="cargo" name="cargo" class="form-input @error('cargo') form-input--error @enderror" value="{{ old('cargo', $associado->cargo) }}" required>
                        @error('cargo')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="oab" class="form-label">OAB</label>
                        <input type="text" id="oab" name="oab" class="form-input" value="{{ old('oab', $associado->oab) }}">
                    </div>
                    <div class="form-group">
                        <label for="ordem" class="form-label">Ordem de Exibição</label>
                        <input type="number" id="ordem" name="ordem" class="form-input" value="{{ old('ordem', $associado->ordem) }}" min="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $associado->email) }}">
                    </div>
                    <div class="form-group">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-input" value="{{ old('telefone', $associado->telefone) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="linkedin" class="form-label">LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" class="form-input" value="{{ old('linkedin', $associado->linkedin) }}">
                </div>

                <div class="form-group">
                    <label for="areas_atuacao" class="form-label">Áreas de Atuação</label>
                    <input type="text" id="areas_atuacao" name="areas_atuacao" class="form-input" value="{{ old('areas_atuacao', is_array($associado->areas_atuacao) ? implode(', ', $associado->areas_atuacao) : $associado->areas_atuacao) }}">
                </div>

                <div class="form-group">
                    <label for="bio" class="form-label">Biografia</label>
                    <textarea id="bio" name="bio" class="form-input" rows="4">{{ old('bio', $associado->bio) }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="foto" class="form-label">Foto</label>
                        @if($associado->foto)
                            <div style="margin-bottom: 10px;">
                                <img src="{{ Storage::url($associado->foto) }}" alt="Foto atual" style="max-width: 100px; border-radius: 8px;">
                            </div>
                        @endif
                        <input type="file" id="foto" name="foto" class="form-input" accept="image/*">
                    </div>
                    <div class="form-group form-group--checkbox" style="padding-top: 32px;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" class="checkbox-input" {{ old('is_active', $associado->is_active) ? 'checked' : '' }}>
                            <span class="checkbox-text">Ativo</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card__footer">
                <a href="{{ route('admin.associados.index') }}" class="btn btn--secondary">Cancelar</a>
                <button type="submit" class="btn btn--primary">Salvar</button>
            </div>
        </form>
    </div>
@endsection
