@extends('admin.layouts.app')

@section('title', 'Novo Usuário')
@section('page-title', 'Novo Usuário')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn--secondary">← Voltar</a>
@endsection

@section('content')
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Dados do Usuário</h2>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="card__body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" id="name" name="name" class="form-input @error('name') form-input--error @enderror" value="{{ old('name') }}" required>
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">E-mail *</label>
                        <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email') }}" required>
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Senha *</label>
                        <input type="password" id="password" name="password" class="form-input @error('password') form-input--error @enderror" required minlength="6">
                        <span class="form-hint">Mínimo de 6 caracteres</span>
                        @error('password')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmar Senha *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
                        <span class="form-hint">Digite a mesma senha novamente</span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role" class="form-label">Perfil *</label>
                        <select id="role" name="role" class="form-input form-select @error('role') form-input--error @enderror" required>
                            <option value="">Selecione...</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor</option>
                            <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Cliente</option>
                        </select>
                        @error('role')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="form-group form-group--checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" class="checkbox-input" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="checkbox-text">Ativo</span>
                    </label>
                </div>
            </div>
            <div class="card__footer">
                <a href="{{ route('admin.users.index') }}" class="btn btn--secondary">Cancelar</a>
                <button type="submit" class="btn btn--primary">Salvar</button>
            </div>
        </form>
    </div>
@endsection
