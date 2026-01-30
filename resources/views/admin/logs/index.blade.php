@extends('admin.layouts.app')

@section('title', 'Logs de Atividade')
@section('page-title', 'Logs de Atividade')

@section('content')
    <form action="{{ route('admin.logs.index') }}" method="GET" class="filter-bar">
        <input type="text" name="search" class="form-input" placeholder="Buscar na descrição..." value="{{ request('search') }}">
        <select name="action" class="form-input form-select">
            <option value="">Todas as ações</option>
            @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
            @endforeach
        </select>
        <select name="user_id" class="form-input form-select">
            <option value="">Todos os usuários</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}" placeholder="De">
        <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}" placeholder="Até">
        <button type="submit" class="btn btn--primary">Filtrar</button>
        @if(request()->hasAny(['search', 'action', 'user_id', 'date_from', 'date_to']))
            <a href="{{ route('admin.logs.index') }}" class="btn btn--secondary">Limpar</a>
        @endif
    </form>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Descrição</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user?->name ?? 'Sistema' }}</td>
                    <td>
                        <span class="badge 
                            @if(in_array($log->action, ['login']))badge--success
                            @elseif(in_array($log->action, ['logout']))badge--gray
                            @elseif(in_array($log->action, ['create']))badge--info
                            @elseif(in_array($log->action, ['update']))badge--warning
                            @elseif(in_array($log->action, ['delete']))badge--danger
                            @else badge--gray
                            @endif
                        ">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td>{{ Str::limit($log->description, 50) }}</td>
                    <td><code style="font-size: 12px;">{{ $log->ip_address ?? '-' }}</code></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-message">Nenhum log encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->withQueryString()->links() }}
@endsection
