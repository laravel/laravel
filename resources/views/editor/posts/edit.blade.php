@extends('editor.layouts.app')

@section('title', 'Editar Artigo')
@section('page-title', 'Editar Artigo')

@section('header-actions')
    <a href="{{ route('editor.posts.index') }}" class="btn btn--secondary">← Voltar</a>
@endsection

@section('content')
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Editar Artigo</h2>
            <span class="badge {{ $post->status === 'publicado' ? 'badge--success' : 'badge--gray' }}">
                {{ ucfirst($post->status) }}
            </span>
        </div>
        <form action="{{ route('editor.posts.update', $post) }}" method="POST" id="post-form">
            @csrf
            @method('PUT')
            <div class="card__body">
                <div class="form-group">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" id="titulo" name="titulo" class="form-input @error('titulo') form-input--error @enderror" value="{{ old('titulo', $post->titulo) }}" required tabindex="1">
                    @error('titulo')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="resumo" class="form-label">Resumo</label>
                    <textarea id="resumo" name="resumo" class="form-input" rows="2" maxlength="500" tabindex="2">{{ old('resumo', $post->resumo) }}</textarea>
                    <span class="form-hint">Será exibido na listagem do blog</span>
                </div>

                <div class="form-group">
                    <label for="conteudo" class="form-label">Conteúdo *</label>
                    <div id="editor-container" tabindex="-1"></div>
                    <textarea id="conteudo" name="conteudo" class="form-input @error('conteudo') form-input--error @enderror" style="display:none;">{{ old('conteudo', $post->conteudo) }}</textarea>
                    <span id="conteudo-error" class="form-error" style="display:none;">O conteúdo é obrigatório.</span>
                    @error('conteudo')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select id="categoria" name="categoria" class="form-input form-select" tabindex="3">
                            <option value="">Selecione uma categoria</option>
                            <option value="Direito Civil" {{ old('categoria', $post->categoria) == 'Direito Civil' ? 'selected' : '' }}>Direito Civil</option>
                            <option value="Direito Trabalhista" {{ old('categoria', $post->categoria) == 'Direito Trabalhista' ? 'selected' : '' }}>Direito Trabalhista</option>
                            <option value="Direito Empresarial" {{ old('categoria', $post->categoria) == 'Direito Empresarial' ? 'selected' : '' }}>Direito Empresarial</option>
                            <option value="Direito Tributário" {{ old('categoria', $post->categoria) == 'Direito Tributário' ? 'selected' : '' }}>Direito Tributário</option>
                            <option value="Direito Imobiliário" {{ old('categoria', $post->categoria) == 'Direito Imobiliário' ? 'selected' : '' }}>Direito Imobiliário</option>
                            <option value="Direito Contratual" {{ old('categoria', $post->categoria) == 'Direito Contratual' ? 'selected' : '' }}>Direito Contratual</option>
                            <option value="Direito do Consumidor" {{ old('categoria', $post->categoria) == 'Direito do Consumidor' ? 'selected' : '' }}>Direito do Consumidor</option>
                            <option value="Direito de Família" {{ old('categoria', $post->categoria) == 'Direito de Família' ? 'selected' : '' }}>Direito de Família</option>
                            <option value="Notícias" {{ old('categoria', $post->categoria) == 'Notícias' ? 'selected' : '' }}>Notícias</option>
                            <option value="Artigos" {{ old('categoria', $post->categoria) == 'Artigos' ? 'selected' : '' }}>Artigos</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" id="tags" name="tags" class="form-input" value="{{ old('tags', is_array($post->tags) ? implode(', ', $post->tags) : $post->tags) }}" tabindex="4">
                    </div>
                </div>

                <div class="form-group form-group--checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" class="checkbox-input" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }} tabindex="5">
                        <span class="checkbox-text">Artigo em destaque</span>
                    </label>
                </div>

                <div style="padding: 16px; background: var(--gray-50); border-radius: var(--border-radius); margin-top: 16px;">
                    <p style="font-size: 13px; color: var(--gray-500); margin: 0;">
                        <strong>Criado:</strong> {{ $post->created_at->format('d/m/Y H:i') }} • 
                        <strong>Atualizado:</strong> {{ $post->updated_at->format('d/m/Y H:i') }} • 
                        <strong>Views:</strong> {{ number_format($post->views) }}
                        @if($post->published_at)
                            • <strong>Publicado:</strong> {{ $post->published_at->format('d/m/Y H:i') }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="card__footer" style="justify-content: space-between;">
                <div>
                    @if($post->status !== 'publicado')
                    <form action="{{ route('editor.posts.destroy', $post) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja remover este rascunho?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--outline" tabindex="9">Excluir Rascunho</button>
                    </form>
                    @endif
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn btn--secondary" tabindex="6">Salvar</button>
                    @if($post->status !== 'publicado')
                    <button type="button" class="btn btn--success" onclick="document.getElementById('publish-form').submit();" tabindex="7">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Publicar
                    </button>
                    @else
                    <button type="button" class="btn btn--outline" onclick="document.getElementById('unpublish-form').submit();" tabindex="7">
                        Despublicar
                    </button>
                    @endif
                </div>
            </div>
        </form>

        <!-- Hidden forms for publish/unpublish -->
        <form id="publish-form" action="{{ route('editor.posts.publish', $post) }}" method="POST" style="display: none;">
            @csrf
        </form>
        <form id="unpublish-form" action="{{ route('editor.posts.unpublish', $post) }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
    #editor-container {
        height: 400px;
        background: white;
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
    }
    .ql-toolbar.ql-snow {
        border-top-left-radius: var(--border-radius);
        border-top-right-radius: var(--border-radius);
        border-color: var(--gray-300);
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: var(--border-radius);
        border-bottom-right-radius: var(--border-radius);
        border-color: var(--gray-300);
        font-size: 15px;
    }
    .btn--outline {
        background: transparent;
        border: 1px solid currentColor;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Escreva seu artigo aqui...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['blockquote', 'code-block'],
                ['link'],
                ['clean']
            ]
        }
    });

    // Load existing content
    const existingContent = document.getElementById('conteudo').value;
    if (existingContent) {
        quill.root.innerHTML = existingContent;
    }

    // Sync content before submit and validate
    document.getElementById('post-form').addEventListener('submit', function(e) {
        const content = quill.root.innerHTML;
        const errorEl = document.getElementById('conteudo-error');
        
        // Check if content is empty
        if (!content || content === '<p><br></p>' || content.trim() === '') {
            e.preventDefault();
            errorEl.style.display = 'block';
            return false;
        }
        
        errorEl.style.display = 'none';
        document.getElementById('conteudo').value = content;
    });
</script>
@endpush
