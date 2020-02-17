<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
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
            trans('backoffice::auth.first_name'),
            trans('backoffice::auth.last_name'),
            trans('backoffice::auth.email'),
            trans('backoffice::auth.username'),
            trans('backoffice::auth.activated'),
            trans('backoffice::auth.last_login'),
        ];
    }
}
