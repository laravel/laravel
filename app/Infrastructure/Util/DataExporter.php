<?php

namespace App\Infrastructure\Util;

use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class DataExporter
{
    const EXTENSION_XLS = 'xls';

    /** @var Excel */
    private $excel;
    /** @var string */
    private $sheetName = 'WorkSheet 1';
    /** @var array */
    private $columnFormats = [];
    /** @var array */
    private $cellWrappings = [];

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    /**
     * @param string $sheetName
     *
     * @return DataExporter
     */
    public function setSheetName(string $sheetName): self
    {
        $this->sheetName = $sheetName;

        return $this;
    }

    /**
     * @param array $columnFormats
     *
     * @return DataExporter
     */
    public function setColumnFormats(array $columnFormats): self
    {
        $this->columnFormats = $columnFormats;

        return $this;
    }

    /**
     * @param array $cellWrappings
     *
     * @return DataExporter
     */
    public function setCellWrappings(array $cellWrappings): self
    {
        $this->cellWrappings = $cellWrappings;

        return $this;
    }

    /**
     * @param array $data
     * @param string $fileName
     */
    public function xls(array $data, string $fileName)
    {
        $this->excel($data, $fileName)->download(static::EXTENSION_XLS, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    private function excel(
        array $data,
        string $fileName
    ) {
        // Create excel writer
        return $this->excel->create($fileName, function (LaravelExcelWriter $excel) use ($data) {
            $excel->sheet($this->sheetName, function (LaravelExcelWorksheet $sheet) use ($data) {
                $sheet->setColumnFormat($this->columnFormats);

                $this->addCellWraps($sheet);

                $sheet->fromArray($data);

                // heading translations
                $keys = empty($data) ? [] : array_keys(head($data));
                $sheet->row(1, $this->columnTranslations($keys));

                // heading style
                $sheet->row(1, function (CellWriter $row) {
                    $row->setFontWeight('bold');
                });
                $sheet->freezeFirstRow();
            });
        });
    }

    /**
     * Translates given keys with lang files.
     *
     * @param  array $array_keys
     *
     * @return array
     */
    private function columnTranslations(array $array_keys)
    {
        return array_map(function ($key) {
            return trans($key);
        }, $array_keys);
    }

    private function addCellWraps(LaravelExcelWorksheet $sheet)
    {
        foreach ($this->cellWrappings as $cell => $wrap) {
            $sheet->getStyle($cell)->getAlignment()->setWrapText($wrap);
        }
    }
}
