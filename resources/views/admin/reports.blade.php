@extends('layouts.dashboard')

@section('title', 'Laporan - Admin')

@section('content')
<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-1">Laporan Sistem</h1>
        <p class="text-slate-600">Kelola semua laporan dari siswa dan guru</p>
    </div>

    <div class="border border-slate-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-semibold text-slate-900">Daftar Laporan</h2>
        </div>
        <div class="overflow-x-auto">
            @if($reports->count() > 0)
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Judul</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Pembuat</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Status</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Tanggal</th>
                            <th class="px-6 py-3 text-left font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-6 py-3 text-slate-900 font-medium">{{ $report->title ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-slate-700">{{ $report->user->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-medium
                                        @if($report->status === 'open') bg-red-100 text-red-800
                                        @elseif($report->status === 'process') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($report->status ?? 'open') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-slate-600 text-sm">{{ $report->created_at->format('d M Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <a href="{{ route('reports.show', $report) }}" class="text-slate-600 hover:text-slate-900">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center text-slate-500">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm">Tidak ada laporan</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection