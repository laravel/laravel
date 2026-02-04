<x-app-layout>
    <div class="min-h-screen bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold">Kelola Laporan</h1>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($reports->count())
                <div class="space-y-4">
                    @foreach($reports as $report)
                        <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $report->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($report->description, 140) }}</p>
                                <p class="text-xs text-gray-500 mt-2">Pelapor: {{ $report->user->name }} • {{ $report->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded text-sm font-medium text-white
                                    @if($report->status === 'open') bg-red-600
                                    @elseif($report->status === 'process') bg-yellow-600
                                    @else bg-green-600
                                    @endif">
                                    {{ ucfirst($report->status) }}
                                </span>
                                <div class="mt-3">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat & Tindak</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="border border-dashed border-gray-300 rounded-lg p-12 text-center">
                    <p class="text-gray-500">Belum ada laporan</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
