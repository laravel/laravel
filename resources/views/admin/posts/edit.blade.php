@extends('admin.layouts.app')

@section('title', 'Editar Post')
@section('page-title', 'Editar Post')

@section('header-actions')
    <a href="{{ route('admin.posts.index') }}" class="btn btn--secondary">← Voltar</a>
@endsection

@section('content')
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Editar Post</h2>
        </div>
        <form action="{{ route('admin.posts.update', $post) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card__body">
                <div class="form-group">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" id="titulo" name="titulo" class="form-input @error('titulo') form-input--error @enderror" value="{{ old('titulo', $post->titulo) }}" required>
                    @error('titulo')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="resumo" class="form-label">Resumo</label>
                    <textarea id="resumo" name="resumo" class="form-input" rows="2">{{ old('resumo', $post->resumo) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="conteudo" class="form-label">Conteúdo *</label>
                    <textarea id="conteudo" name="conteudo" class="form-input @error('conteudo') form-input--error @enderror" rows="15" required>{{ old('conteudo', $post->conteudo) }}</textarea>
                    @error('conteudo')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria" class="form-label">Categoria</label>
                        <input type="text" id="categoria" name="categoria" class="form-input" value="{{ old('categoria', $post->categoria) }}" placeholder="Ex: Direito Civil, Trabalhista...">
                    </div>
                    <div class="form-group">
                        <label for="status" class="form-label">Status *</label>
                        <select id="status" name="status" class="form-input form-select" required>
                            <option value="rascunho" {{ old('status', $post->status) == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                            <option value="publicado" {{ old('status', $post->status) == 'publicado' ? 'selected' : '' }}>Publicado</option>
                            <option value="arquivado" {{ old('status', $post->status) == 'arquivado' ? 'selected' : '' }}>Arquivado</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group--checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" class="checkbox-input" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                        <span class="checkbox-text">Post em destaque</span>
                    </label>
                </div>
            </div>
            <div class="card__footer">
                <a href="{{ route('admin.posts.index') }}" class="btn btn--secondary">Cancelar</a>
                <button type="submit" class="btn btn--primary">Salvar</button>
            </div>
        </form>
    </div>
@endsection
