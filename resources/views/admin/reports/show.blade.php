<x-app-layout>
    <div class="min-h-screen bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Detail Laporan</h1>
                <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold">{{ $report->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">Pelapor: {{ $report->user->name }} • {{ $report->created_at->format('d M Y H:i') }}</p>

                <div class="mt-4 text-gray-700">
                    {!! nl2br(e($report->description)) !!}
                </div>

                <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="mt-6">
                    @csrf
                    @method('PUT')

                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="open" {{ $report->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="process" {{ $report->status === 'process' ? 'selected' : '' }}>In Process</option>
                        <option value="closed" {{ $report->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>

                    <div class="mt-4 flex items-center space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                        <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-600">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
