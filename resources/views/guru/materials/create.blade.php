<x-app-layout>
    <div class="min-h-screen bg-white">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Tambah Materi</h1>
                <p class="text-gray-600 mt-1">Upload materi pembelajaran baru</p>
            </div>

            <form action="{{ route('guru.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-900">Judul</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-900">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-900">File</label>
                    <div class="mt-1 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-gray-400 transition">
                        <input type="file" id="file" name="file" required class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <label for="file" class="cursor-pointer">
                            <p class="text-gray-600">Pilih file atau drag & drop</p>
                            <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, XLS, XLSX (max 10MB)</p>
                        </label>
                    </div>
                    @error('file') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-medium hover:bg-blue-700 transition">Simpan</button>
                    <a href="{{ route('guru.materials.index') }}" class="bg-gray-200 text-gray-900 px-6 py-2 rounded font-medium hover:bg-gray-300 transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
