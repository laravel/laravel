<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\PaginationRequest;
use App\Http\Util\RouteDefiner;
use App\Infrastructure\Util\DataExporter;
use App\Infrastructure\Util\PaginationData;
use Digbang\Security\Roles\Role;
use Digbang\Security\Users\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class RoleExportHandler extends Handler implements RouteDefiner
{
    use PaginationRequest;

    public function __invoke(Request $request, DataExporter $exporter)
    {
        $items = new Collection($this->getData($request));
        $items = $items->map(function (Role $role) {
            $users = [];

            /** @var User $user */
            foreach ($role->getUsers() as $user) {
                $users[] = $user->getName();
            }

            return [
                'Id' => $role->getRoleId(),
                trans('backoffice::auth.name') => $role->getName(),
                trans('backoffice::auth.users') => implode(', ', $users),
            ];
        })->toArray();

        $filename = (new \DateTime())->format('Ymd') . ' - ' . trans('backoffice::auth.roles');
        $exporter
            ->setSheetName(trans('backoffice::auth.roles'))
            ->xls(
                $items,
                $filename
            );
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->get("$backofficePrefix/$routePrefix/export", [
                'uses' => static::class,
                'permission' => Permission::ROLE_EXPORT,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route(array $filter): string
    {
        return route(static::class, $filter);
    }

    private function getData(Request $request)
    {
        /** @var \Digbang\Backoffice\Repositories\DoctrineRoleRepository $roles */
        $roles = security()->roles();

        $paginationData = $this->getSorting($request);

        return $roles->search(
            $request->all(['name', 'permission']),
            $this->convertSorting($paginationData),
            $paginationData->getLimit(),
            $paginationData->getOffset()
        );
    }

    private function getSorting(Request $request): PaginationData
    {
        $paginationData = $this->paginationBackofficeDataWithoutLimit($request);

        if ($paginationData->getSorting()->isEmpty()) {
            $paginationData->addSort('name');
        }

        return $paginationData;
    }

    /*
     * This is only needed when using any of the digbang/backoffice package repositories
     */
    private function convertSorting(PaginationData $paginationData): array
    {
        $sortings = [
            'name' => 'r.name',
        ];

        $selectedSorts = $paginationData->getSorting();

        $orderBy = [];
        foreach ($selectedSorts as $key => $sense) {
            $key = $sortings[$key];
            $orderBy[$key] = $sense;
        }

        return $orderBy;
    }
}
