@extends('editor.layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')

@section('content')
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Dados Pessoais</h2>
        </div>
        <form action="{{ route('editor.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card__body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" id="name" name="name" class="form-input @error('name') form-input--error @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">E-mail *</label>
                        <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                </div>

                <hr style="border: none; border-top: 1px solid var(--gray-200); margin: 24px 0;">

                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--gray-700);">Alterar Senha</h3>
                <p style="font-size: 14px; color: var(--gray-500); margin-bottom: 16px;">Deixe em branco se não quiser alterar a senha.</p>

                <div class="form-group">
                    <label for="current_password" class="form-label">Senha Atual</label>
                    <input type="password" id="current_password" name="current_password" class="form-input @error('current_password') form-input--error @enderror">
                    <span class="form-hint">Necessária apenas para alterar a senha</span>
                    @error('current_password')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Nova Senha</label>
                        <input type="password" id="password" name="password" class="form-input @error('password') form-input--error @enderror" minlength="6">
                        <span class="form-hint">Mínimo de 6 caracteres</span>
                        @error('password')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input">
                        <span class="form-hint">Digite a mesma senha novamente</span>
                    </div>
                </div>

                <div style="padding: 16px; background: var(--gray-50); border-radius: var(--border-radius); margin-top: 16px;">
                    <p style="font-size: 13px; color: var(--gray-500); margin: 0;">
                        <strong>Membro desde:</strong> {{ $user->created_at->format('d/m/Y') }} • 
                        <strong>Último acesso:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
            <div class="card__footer">
                <button type="submit" class="btn btn--primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
@endsection
