<x-template>
    <div class="container">
        <div class="mb-3 text-end">
            <a href="{{ route('article.edit', ['id' => $article->id]) }}" class="btn
                btn-info">Ubah</a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                data-bs-target="#deleteModal">Hapus</button>
            <a href="{{ route('article.list') }}" class="btn
    btn-secondary">Kembali</a>
        </div>
        <h1>{{ $article->title }}</h5>
            <h5 class="mb-2 text-body-secondary">{{ $article->updated_at }}</h6>
                <p>
                    {{ $article->content }}
                </p>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h1 class="modal-title fs-5">Hapus artikel</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin akan menghapus artikel?
                </div>
                <div class="modal-footer">
                    <form method="post" action="{{ route('article.delete', ['id' => $article->id]) }}">
                        @csrf
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-template>
