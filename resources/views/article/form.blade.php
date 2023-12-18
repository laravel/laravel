<!DOCTYPE html>
<html>

<head>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>
</head>

<body>
    <x-template>
        <div class="container">
            <form method="post" class="was-validated">
                @csrf
                @isset($article)
                    <x-form.group for="slug" label="Slug">
                        <input type="text" name="slug" id="slug" class="form-control"
                            value="{{ old('slug') ?? ($article->slug ?? '') }}" required>
                    </x-form.group>
                @endisset
                <x-form.group for="title" label="Judul">
                    <input type="text" name="title" id="title" class="form-control"
                        value="{{ old('title') ?? ($article->title ?? '') }}" required>
                </x-form.group>
                <x-form.group for="content" label="Isi">
                    <textarea name="content" id="content" class="form-control" required>{{ old('content') ?? ($article->content ?? '') }}</textarea>
                </x-form.group>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </x-template>
</body>
