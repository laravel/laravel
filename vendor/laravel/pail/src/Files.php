<?php

namespace Laravel\Pail;

use Illuminate\Support\Collection;

class Files
{
    /**
     * Creates a new instance of the files.
     */
    public function __construct(
        protected string $path,
    ) {
        //
    }

    /**
     * Returns the list of files.
     *
     * @return Collection<int, File>
     */
    public function all(): Collection
    {
        $files = glob($this->path.'/*.pail') ?: [];

        return collect($files)
            ->map(fn (string $file) => new File($file));
    }
}
