<?php

namespace App\Infrastructure\Util;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

class ExcelImporter extends DataImporter
{
    /** @var Excel */
    protected $excel;

    /** @var LaravelExcelReader */
    protected $reader;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    /** {@inheritdoc} */
    public function fromUploadedFile(UploadedFile $uploadedFile): DataImporter
    {
        $this->reader = $this->excel->load($uploadedFile);

        return $this;
    }

    /** {@inheritdoc} */
    public function next(int $count)
    {
        $rows = $this->reader->takeRows($this->cursor)->all();
        $this->reader->skipRows($this->cursor += $count);

        if ($rows->count() === 0) {
            $this->isEndOfFile = true;
        }

        return $rows->all();
    }
}
