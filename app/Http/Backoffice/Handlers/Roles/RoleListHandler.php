<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleCriteriaRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Backoffice\Listings\Listing;
use Digbang\Backoffice\Repositories\DoctrineRoleRepository;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Digbang\Security\Users\User;
use Digbang\Utils\Sorting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use ProjectName\Repositories\Criteria\Roles\RoleFilter;
use ProjectName\Repositories\Criteria\Roles\RoleSorting;

class RoleListHandler extends Handler implements RouteDefiner
{
    private PermissionParser $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(RoleCriteriaRequest $request): View
    {
        $list = $this->getListing();

        $this->buildFilters($list);
        $this->buildListActions($list, $request);

        $list->fill($this->getData($request));

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('backoffice::auth.roles'),
        ]);

        return view()->make('backoffice::index', [
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
                'uses' => self::class,
                'permission' => Permission::ROLE_LIST,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(): string
    {
        return route(self::class);
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

        $listing->addValueExtractor('id', function (Role $role): int {
            return $role->getRoleId();
        });

        $listing->addValueExtractor('slug', function (Role $role): string {
            return $role->getRoleSlug();
        });

        $listing->addValueExtractor('users', function (Role $role): string {
            $users = [];

            /** @var User[] $roleUsers */
            $roleUsers = $role->getUsers();
            foreach ($roleUsers as $user) {
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

        $filters->text(RoleFilter::NAME, trans('backoffice::auth.name'), ['class' => 'form-control']);
        $filters->dropdown(
            RoleFilter::PERMISSION,
            trans('backoffice::auth.permissions'),
            $this->permissionParser->toDropdownArray(security()->permissions()->all(), true),
            ['class' => 'form-control']
        );
    }

    private function buildListActions(Listing $list, RoleCriteriaRequest $request): void
    {
        $actions = backoffice()->actions();

        $actions->link(function () {
            try {
                return security()->url()->to(RoleCreateFormHandler::route());
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('plus') . ' ' . trans('backoffice::default.new', ['model' => trans('backoffice::auth.role')]),
        [
            'class' => 'btn btn-primary',
        ]);

        $actions->link(function () use ($request) {
            try {
                return security()->url()->to(RoleExportHandler::route($request->all()));
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('file-excel-o') . ' ' . trans('backoffice::default.export'),
        [
            'class' => 'btn btn-success',
        ]);

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
                return security()->url()->to(RoleEditFormHandler::route($row->get('id')));
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

    /**
     * @return array|\Illuminate\Pagination\LengthAwarePaginator
     */
    private function getData(RoleCriteriaRequest $request)
    {
        /** @var DoctrineRoleRepository $roles */
        $roles = security()->roles();

        $filter = $request->getFilter()->values();
        $sorting = $this->convertSorting($request->getSorting());
        $limit = $request->getPaginationData()->getLimit();
        $offset = $request->getPaginationData()->getOffset();

        return $roles->search($filter, $sorting, $limit, $offset);
    }

    /*
     * This is only needed when using any of the digbang/backoffice package repositories
     */
    private function convertSorting(Sorting $roleSorting): array
    {
        $sortings = [
            RoleSorting::NAME => 'r.name',
        ];

        $selectedSorts = $roleSorting->get(array_keys($sortings));
        if (count($selectedSorts) === 0) {
            $selectedSorts = [array_first(array_keys($sortings)) => 'ASC'];
        }

        $orderBy = [];
        foreach ($selectedSorts as $key => $sense) {
            $key = $sortings[$key];
            $orderBy[$key] = $sense;
        }

        return $orderBy;
    }
}
