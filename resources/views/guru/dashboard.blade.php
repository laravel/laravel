@extends('layouts.dashboard')

@section('title', 'Dashboard Guru - Materi')

@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold mb-1">Materi Pembelajaran</h1>
            <p class="text-slate-600">Upload & kelola materi Anda</p>
        </div>
        <button id="openUploadForm" class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors text-sm">
            + Tambah Materi
        </button>
    </div>

    <!-- Upload Form -->
    <div id="uploadFormSection" class="hidden border border-slate-200 rounded-lg p-6 mb-8 bg-slate-50">
        <h2 class="text-lg font-semibold mb-4">Upload Materi Baru</h2>
        
        <form action="{{ route('guru.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Judul Materi</label>
                <input type="text" id="title" name="title" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Judul materi...">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Jelaskan materi..."></textarea>
            </div>

            <div>
                <label for="file" class="block text-sm font-medium text-slate-700 mb-1">File</label>
                <input type="file" id="file" name="file" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <p class="text-xs text-slate-500 mt-1">PDF, DOC, DOCX (Max 10MB)</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm hover:bg-slate-800">Upload</button>
                <button type="button" id="closeUploadForm" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-50">Batal</button>
            </div>
        </form>
    </div>

    <!-- Materials Grid -->
    @if($materials->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($materials as $material)
                <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="w-full h-20 bg-slate-100 rounded-md mb-4 flex items-center justify-center">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                        </svg>
                    </div>
                    
                    <h3 class="font-semibold text-slate-900 line-clamp-2 mb-2 text-sm">{{ $material->title }}</h3>
                    @if($material->description)
                        <p class="text-xs text-slate-600 line-clamp-2 mb-3">{{ $material->description }}</p>
                    @endif
                    
                    <div class="text-xs text-slate-500 mb-4">
                        {{ $material->created_at->format('d M Y') }}
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('siswa.materials.download', $material) }}" class="flex-1 px-3 py-2 bg-slate-900 text-white text-center text-xs rounded-lg hover:bg-slate-800 transition-colors">Download</a>
                        <a href="{{ route('guru.materials.edit', $material) }}" class="px-3 py-2 border border-slate-200 text-slate-700 text-xs rounded-lg hover:bg-slate-50">Edit</a>
                        <form action="{{ route('guru.materials.destroy', $material) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 border border-red-200 text-red-600 text-xs rounded-lg hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if($materials->hasPages())
            <div class="mt-8">
                {{ $materials->links() }}
            </div>
        @endif
    @else
        <div class="border border-slate-200 rounded-lg p-12 text-center bg-slate-50">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Belum ada materi</h3>
            <p class="text-slate-600 mb-4">Upload materi pembelajaran Anda</p>
            <button id="openUploadForm2" class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 text-sm">
                + Upload Materi
            </button>
        </div>
    @endif
</div>

<script>
    const uploadForm = document.getElementById('uploadFormSection');
    const openBtns = document.querySelectorAll('#openUploadForm, #openUploadForm2');
    const closeBtn = document.getElementById('closeUploadForm');

    openBtns.forEach(btn => {
        btn?.addEventListener('click', () => uploadForm.classList.remove('hidden'));
    });

    closeBtn?.addEventListener('click', () => uploadForm.classList.add('hidden'));
</script>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Upload Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Materi Pembelajaran</h1>
            <p class="text-slate-600 mt-1">Kelola materi pembelajaran Anda</p>
        </div>
        <button id="openUploadForm" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors">
            <span class="text-lg mr-2">+</span>
            Tambah Materi
        </button>
    </div>

    <!-- Upload Form (Hidden by default) -->
    <div id="uploadFormSection" class="hidden bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Upload Materi Baru</h2>
        
        <form action="{{ route('guru.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Judul Materi</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    required 
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                    placeholder="Contoh: Pengenalan Algoritma"
                >
                @error('title')
                    <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="3"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                    placeholder="Jelaskan materi ini..."
                ></textarea>
                @error('description')
                    <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- File Upload -->
            <div>
                <label for="file" class="block text-sm font-medium text-slate-700 mb-1">File Materi</label>
                <input 
                    type="file" 
                    id="file" 
                    name="file" 
                    required 
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                >
                <p class="text-xs text-slate-500 mt-1">Format: PDF, DOC, DOCX (Max 10MB)</p>
                @error('file')
                    <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-2">
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors"
                >
                    Upload Materi
                </button>
                <button 
                    type="button" 
                    id="closeUploadForm"
                    class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors"
                >
                    Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($materials as $material)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                <!-- Document Icon -->
                <div class="bg-slate-50 p-6 flex items-center justify-center min-h-[150px]">
                    <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                    </svg>
                </div>

                <!-- Content -->
                <div class="p-4 space-y-3">
                    <h3 class="font-semibold text-slate-900 line-clamp-2">{{ $material->title }}</h3>
                    
                    @if($material->description)
                        <p class="text-sm text-slate-600 line-clamp-2">{{ $material->description }}</p>
                    @endif

                    <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                        <span>{{ $material->created_at->format('d M Y') }}</span>
                        <span>{{ number_format($material->downloads ?? 0, 0) }} download</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-3">
                        <a 
                            href="{{ route('siswa.materials.download', $material) }}" 
                            class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-slate-900 text-white text-sm rounded-lg hover:bg-slate-800 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </a>
                        <a 
                            href="{{ route('guru.materials.edit', $material) }}" 
                            class="px-3 py-2 border border-slate-200 text-slate-700 text-sm rounded-lg hover:bg-slate-50 transition-colors"
                            title="Edit"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('guru.materials.destroy', $material) }}" method="POST" class="inline" onsubmit="return confirm('Hapus materi ini?');">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                class="px-3 py-2 border border-slate-200 text-red-600 text-sm rounded-lg hover:bg-red-50 transition-colors"
                                title="Hapus"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-slate-200 rounded-xl p-12 text-center">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">Belum ada materi</h3>
                <p class="text-slate-600 mb-4">Mulai dengan membuat materi pembelajaran baru</p>
                <button id="openUploadForm2" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors">
                    <span class="text-lg mr-2">+</span>
                    Tambah Materi Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($materials->hasPages())
        <div class="flex justify-center mt-8">
            {{ $materials->links() }}
        </div>
    @endif
</div>

<!-- Toggle Upload Form Script -->
<script>
    const uploadForm = document.getElementById('uploadFormSection');
    const openBtns = document.querySelectorAll('#openUploadForm, #openUploadForm2');
    const closeBtn = document.getElementById('closeUploadForm');

    openBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            uploadForm.classList.remove('hidden');
        });
    });

    closeBtn.addEventListener('click', () => {
        uploadForm.classList.add('hidden');
    });
</script>
@endsection
