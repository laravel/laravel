<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\PaginationRequest;
use App\Http\Util\RouteDefiner;
use App\Infrastructure\Util\PaginationData;
use Digbang\Backoffice\Listings\Listing;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Digbang\Security\Users\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class RoleListHandler extends Handler implements RouteDefiner
{
    use PaginationRequest;

    /** @var PermissionParser */
    private $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(Request $request, Factory $view)
    {
        $list = $this->getListing();

        $this->buildFilters($list);
        $this->buildListActions($list, $request);

        $list->fill($this->getData($request));

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardIndexHandler::class,
            trans('backoffice::auth.roles'),
        ]);

        return $view->make('backoffice::index', [
            'title' => trans('backoffice::auth.roles'),
            'list' => $list,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->get("$backofficePrefix/$routePrefix/", [
                'uses' => static::class,
                'permission' => Permission::ROLE_LIST,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
                Kernel::BACKOFFICE_PERSISTENT_LISTING,
            ]);
    }

    public static function route(): string
    {
        return route(static::class);
    }

    private function getListing(): Listing
    {
        $listing = backoffice()->listing([
            'name' => trans('backoffice::auth.name'),
            'users' => trans('backoffice::auth.users'),
            'id', 'slug',
        ]);

        $columns = $listing->columns();
        $columns
            ->hide(['id', 'slug'])
            ->sortable(['name']);

        $listing->addValueExtractor('id', function (Role $role) {
            return $role->getRoleId();
        });

        $listing->addValueExtractor('slug', function (Role $role) {
            return $role->getRoleSlug();
        });

        $listing->addValueExtractor('users', function (Role $role) {
            $users = [];
            foreach ($role->getUsers() as $user) {
                /* @var User $user */
                $users[] = $user->getUsername();
            }

            if (count($users) < 5) {
                return implode(', ', $users);
            }

            return implode(', ', array_slice($users, 0, 4)) . '... (+' . (count($users) - 4) . ')';
        });

        return $listing;
    }

    private function buildFilters(Listing $list): void
    {
        $filters = $list->filters();

        $filters->text('name', trans('backoffice::auth.name'), ['class' => 'form-control']);
        $filters->dropdown(
            'permission',
            trans('backoffice::auth.permissions'),
            $this->permissionParser->toDropdownArray(security()->permissions()->all(), true),
            ['class' => 'form-control']
        );
    }

    private function buildListActions(Listing $list, Request $request): void
    {
        $actions = backoffice()->actions();

        try {
            $actions->link(
                security()->url()->to(RoleCreateHandler::route()),
                fa('plus') . ' ' . trans('backoffice::default.new', ['model' => trans('backoffice::auth.role')]),
                ['class' => 'btn btn-primary']
            );
        } catch (SecurityException $e) {
        }

        try {
            $actions->link(
                security()->url()->to(RoleExportHandler::route($request->all())),
                fa('file-excel-o') . ' ' . trans('backoffice::default.export'),
                ['class' => 'btn btn-success']
            );
        } catch (SecurityException $e) {
        }

        $list->setActions($actions);

        $rowActions = backoffice()->actions();

        $rowActions->link(function (Collection $row) {
            try {
                return security()->url()->to(RoleShowHandler::route($row->get('id')));
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('eye'), [
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'title' => trans('backoffice::default.show'),
        ]);

        $rowActions->link(function (Collection $row) {
            try {
                return security()->url()->to(RoleEditHandler::route($row->get('id')));
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('edit'), [
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'title' => trans('backoffice::default.edit'),
        ]);

        $rowActions->form(
            function (Collection $row) {
                try {
                    return security()->url()->to(RoleDeleteHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('times'),
            Request::METHOD_DELETE,
            [
                'class' => 'text-danger',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-confirm' => trans('backoffice::default.delete-confirm'),
                'title' => trans('backoffice::default.delete'),
            ]
        );

        $list->setRowActions($rowActions);
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
        $paginationData = $this->paginationBackofficeData($request);

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
