@extends('admin.layouts.app')

@section('title', 'Novo Associado')
@section('page-title', 'Novo Associado')

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
        <form action="{{ route('admin.associados.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card__body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" id="nome" name="nome" class="form-input @error('nome') form-input--error @enderror" value="{{ old('nome') }}" required>
                        @error('nome')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="cargo" class="form-label">Cargo *</label>
                        <input type="text" id="cargo" name="cargo" class="form-input @error('cargo') form-input--error @enderror" value="{{ old('cargo') }}" placeholder="Ex: Sócio, Advogado Associado..." required>
                        @error('cargo')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="oab" class="form-label">OAB</label>
                        <input type="text" id="oab" name="oab" class="form-input" value="{{ old('oab') }}" placeholder="Ex: OAB/SP 123.456">
                    </div>
                    <div class="form-group">
                        <label for="ordem" class="form-label">Ordem de Exibição</label>
                        <input type="number" id="ordem" name="ordem" class="form-input" value="{{ old('ordem', 0) }}" min="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-input" value="{{ old('telefone') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="linkedin" class="form-label">LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" class="form-input" value="{{ old('linkedin') }}" placeholder="https://linkedin.com/in/...">
                </div>

                <div class="form-group">
                    <label for="areas_atuacao" class="form-label">Áreas de Atuação</label>
                    <input type="text" id="areas_atuacao" name="areas_atuacao" class="form-input" value="{{ old('areas_atuacao') }}" placeholder="Direito Civil, Direito Trabalhista, Direito Empresarial (separar por vírgula)">
                </div>

                <div class="form-group">
                    <label for="bio" class="form-label">Biografia</label>
                    <textarea id="bio" name="bio" class="form-input" rows="4">{{ old('bio') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" id="foto" name="foto" class="form-input" accept="image/*">
                    </div>
                    <div class="form-group form-group--checkbox" style="padding-top: 32px;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" class="checkbox-input" {{ old('is_active', true) ? 'checked' : '' }}>
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
