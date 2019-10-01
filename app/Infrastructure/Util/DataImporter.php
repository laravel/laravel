<?php

namespace App\Infrastructure\Util;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Collections\RowCollection;

abstract class DataImporter
{
    /** @var int */
    protected $cursor = 0;

    /** @var bool */
    protected $isEndOfFile = false;

    /**
     * @param UploadedFile $uploadedFile
     *
     * @return DataImporter
     */
    abstract public function fromUploadedFile(UploadedFile $uploadedFile): self;

    public function rewind(): void
    {
        $this->cursor = 0;
        $this->isEndOfFile = false;
    }

    /** @return bool */
    public function isEoF(): bool
    {
        return $this->isEndOfFile;
    }

    /**
     * @param int $count
     *
     * @return RowCollection
     */
    abstract public function next(int $count);
}
