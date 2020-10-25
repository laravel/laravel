<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Role;

use Illuminate\Support\Collection;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Rows;

class RolePermissionLayout extends Rows
{
    /**
     * Views.
     *
     * @throws \Throwable
     *
     * @return array
     */
    public function fields(): array
    {
        return $this->generatedPermissionFields($this->query->getContent('permission'));
    }

    /**
     * @param Collection $permissionsRaw
     *
     * @return array
     */
    private function generatedPermissionFields(Collection $permissionsRaw): array
    {
        return $permissionsRaw->map(function ($items, $title) {
            return collect($items)
                ->chunk(3)
                ->map(function (Collection $chunks) use ($title) {
                    return Group::make($this->getCheckBoxGroup($chunks, $title))
                        ->alignEnd()
                        ->autoWidth();
                });
        })
            ->flatten()
            ->toArray();
    }

    /**
     * @param Collection $chunks
     * @param string     $title
     *
     * @return array
     */
    private function getCheckBoxGroup(Collection $chunks, string $title): array
    {
        return $chunks->values()->map(function ($permission, $keys) use ($title) {
            return CheckBox::make('permissions.'.base64_encode($permission['slug']))
                ->placeholder($permission['description'])
                ->value($permission['active'])
                ->title($keys === 0 ? $title : ' ')
                ->sendTrueOrFalse();
        })->toArray();
    }
}
