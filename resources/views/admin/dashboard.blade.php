@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-1">Dashboard Admin</h1>
        <p class="text-slate-600">Kelola sistem LMS</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="border border-slate-200 rounded-lg p-6 bg-white">
            <p class="text-sm text-slate-600 mb-2">Total Siswa</p>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total_siswa'] ?? 0 }}</p>
        </div>
        <div class="border border-slate-200 rounded-lg p-6 bg-white">
            <p class="text-sm text-slate-600 mb-2">Total Guru</p>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total_guru'] ?? 0 }}</p>
        </div>
        <div class="border border-slate-200 rounded-lg p-6 bg-white">
            <p class="text-sm text-slate-600 mb-2">Total Materi</p>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total_materi'] ?? 0 }}</p>
        </div>
        <div class="border border-slate-200 rounded-lg p-6 bg-white">
            <p class="text-sm text-slate-600 mb-2">Laporan Terbuka</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['laporan_open'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Users Management -->
    <div class="border border-slate-200 rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Manajemen Pengguna</h2>
                <a href="javascript:void(0)" class="text-sm text-slate-600 hover:text-slate-900">Lihat Semua</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($users->count() > 0)
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Nama</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Email</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Role</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users->take(5) as $user)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-6 py-3 text-slate-900 font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-3 text-slate-700">{{ $user->email }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-medium
                                        @if($user->role === 'admin') bg-slate-100 text-slate-900
                                        @elseif($user->role === 'guru') bg-slate-100 text-slate-900
                                        @else bg-slate-100 text-slate-900
                                        @endif
                                    ">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm space-x-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-slate-600 hover:text-slate-900">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-600 hover:text-red-600">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-slate-500 text-sm">
                    Tidak ada pengguna
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="border border-slate-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Laporan Terbaru</h2>
                <a href="{{ route('admin.reports') }}" class="text-sm text-slate-600 hover:text-slate-900">Lihat Semua</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($recent_reports->count() > 0)
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Judul</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Pembuat</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Status</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_reports->take(5) as $report)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-6 py-3 text-slate-900 font-medium">{{ $report->title ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-slate-700">{{ $report->user->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-slate-100 text-slate-900">
                                        {{ ucfirst($report->status ?? 'open') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-slate-600 text-sm">{{ $report->created_at->format('d M Y') ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-slate-500 text-sm">
                    Tidak ada laporan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
