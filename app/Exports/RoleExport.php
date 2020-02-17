<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RoleExport implements FromCollection, WithHeadings
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * RoleExport constructor.
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'Id',
            trans('backoffice::auth.name'),
            trans('backoffice::auth.users'),
        ];
    }
}
