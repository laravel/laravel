<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TableExport implements FromCollection //, WithHeadings
{
    public function __construct(private readonly string $model, private readonly array $ids) {
        //
    }

//    public function headings(): array
//    {
//        return [
//        ];
//    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->model::whereIn('id', $this->ids)->get();
    }
}
