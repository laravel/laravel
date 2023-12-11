<x-template title="Form artikel">
    <div class="cointainer">
        <form method="post" class="was-validated">
            @csrf
            <x-form.group for="titile" label="judul">
                <input type="text" name="title" id="title" class="form-control" required>
            </x-form.group>
            <x-form.group for="content" label= "Isi">
                <textarea name="content" id="content" class="form-control" required></textarea>
            </x-form.group>
            <div class="nb-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
    </form>
    </div>
</x-template>
