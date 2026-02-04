<x-app-layout>
    <div class="min-h-screen bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Materi Saya</h1>
                    <p class="text-gray-600 mt-1">Kelola materi pembelajaran</p>
                </div>
                <a href="{{ route('guru.materials.create') }}" class="bg-green-600 text-white px-6 py-2 rounded font-medium hover:bg-green-700 transition">
                    + Tambah
                </a>
            </div>

            @if($materials->count() > 0)
                <div class="space-y-3">
                    @foreach($materials as $material)
                        <div class="border border-gray-200 rounded-lg p-6 flex justify-between items-start hover:bg-gray-50 transition">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $material->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($material->description, 100) }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $material->created_at->format('d M Y') }} {{ $material->file_name ? '• ' . $material->file_name : '' }}</p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('guru.materials.edit', $material) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Edit</a>
                                <form method="POST" action="{{ route('guru.materials.destroy', $material) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium" onclick="return confirm('Hapus materi ini?')">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="border border-dashed border-gray-300 rounded-lg p-12 text-center">
                    <p class="text-gray-500">Belum ada materi</p>
                    <a href="{{ route('guru.materials.create') }}" class="text-blue-600 hover:text-blue-700 mt-2 inline-block">
                        Buat materi pertama →
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
