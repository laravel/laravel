@extends('editor.layouts.app')

@section('title', 'Novo Artigo')
@section('page-title', 'Novo Artigo')

@section('header-actions')
    <a href="{{ route('editor.posts.index') }}" class="btn btn--secondary">← Voltar</a>
@endsection

@section('content')
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Escrever Artigo</h2>
            <span class="badge badge--gray">Rascunho</span>
        </div>
        <form action="{{ route('editor.posts.store') }}" method="POST" id="post-form">
            @csrf
            <div class="card__body">
                <div class="form-group">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" id="titulo" name="titulo" class="form-input @error('titulo') form-input--error @enderror" value="{{ old('titulo') }}" required tabindex="1">
                    @error('titulo')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="resumo" class="form-label">Resumo</label>
                    <textarea id="resumo" name="resumo" class="form-input" rows="2" maxlength="500" placeholder="Breve descrição do artigo (máx. 500 caracteres)" tabindex="2">{{ old('resumo') }}</textarea>
                    <span class="form-hint">Será exibido na listagem do blog</span>
                </div>

                <div class="form-group">
                    <label for="conteudo" class="form-label">Conteúdo *</label>
                    <div id="editor-container" tabindex="-1"></div>
                    <textarea id="conteudo" name="conteudo" class="form-input @error('conteudo') form-input--error @enderror" style="display:none;">{{ old('conteudo') }}</textarea>
                    <span id="conteudo-error" class="form-error" style="display:none;">O conteúdo é obrigatório.</span>
                    @error('conteudo')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select id="categoria" name="categoria" class="form-input form-select" tabindex="3">
                            <option value="">Selecione uma categoria</option>
                            <option value="Direito Civil" {{ old('categoria') == 'Direito Civil' ? 'selected' : '' }}>Direito Civil</option>
                            <option value="Direito Trabalhista" {{ old('categoria') == 'Direito Trabalhista' ? 'selected' : '' }}>Direito Trabalhista</option>
                            <option value="Direito Empresarial" {{ old('categoria') == 'Direito Empresarial' ? 'selected' : '' }}>Direito Empresarial</option>
                            <option value="Direito Tributário" {{ old('categoria') == 'Direito Tributário' ? 'selected' : '' }}>Direito Tributário</option>
                            <option value="Direito Imobiliário" {{ old('categoria') == 'Direito Imobiliário' ? 'selected' : '' }}>Direito Imobiliário</option>
                            <option value="Direito Contratual" {{ old('categoria') == 'Direito Contratual' ? 'selected' : '' }}>Direito Contratual</option>
                            <option value="Direito do Consumidor" {{ old('categoria') == 'Direito do Consumidor' ? 'selected' : '' }}>Direito do Consumidor</option>
                            <option value="Direito de Família" {{ old('categoria') == 'Direito de Família' ? 'selected' : '' }}>Direito de Família</option>
                            <option value="Notícias" {{ old('categoria') == 'Notícias' ? 'selected' : '' }}>Notícias</option>
                            <option value="Artigos" {{ old('categoria') == 'Artigos' ? 'selected' : '' }}>Artigos</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" id="tags" name="tags" class="form-input" value="{{ old('tags') }}" placeholder="direito, advocacia, contratos (separar por vírgula)" tabindex="4">
                    </div>
                </div>

                <div class="form-group form-group--checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" class="checkbox-input" {{ old('is_featured') ? 'checked' : '' }} tabindex="5">
                        <span class="checkbox-text">Artigo em destaque</span>
                    </label>
                </div>

                <div class="save-info" style="padding: 16px; background: var(--gray-50); border-radius: var(--border-radius); margin-top: 16px;">
                    <p style="font-size: 13px; color: var(--gray-600); margin: 0;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        O artigo será salvo como <strong>rascunho</strong>. Você poderá publicá-lo depois na tela de edição.
                    </p>
                </div>
            </div>
            <div class="card__footer">
                <a href="{{ route('editor.posts.index') }}" class="btn btn--secondary" tabindex="7">Cancelar</a>
                <button type="submit" class="btn btn--primary" tabindex="6">Salvar Rascunho</button>
            </div>
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
