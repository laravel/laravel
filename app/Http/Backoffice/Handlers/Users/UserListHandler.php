<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\PaginationRequest;
use App\Http\Util\RouteDefiner;
use App\Infrastructure\Util\PaginationData;
use Digbang\Backoffice\Listings\Listing;
use Digbang\Backoffice\Repositories\DoctrineUserRepository;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Users\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class UserListHandler extends Handler implements RouteDefiner
{
    use PaginationRequest;

    public function __invoke(Request $request, Factory $view)
    {
        $list = $this->getListing();

        $this->buildFilters($list);
        $this->buildListActions($list, $request);

        $list->fill($this->getData($request));

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardIndexHandler::class,
            trans('backoffice::auth.users'),
        ]);

        return $view->make('backoffice::index', [
            'title' => trans('backoffice::auth.users'),
            'list' => $list,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/", [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_LIST,
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
            'firstName' => trans('backoffice::auth.first_name'),
            'lastName' => trans('backoffice::auth.last_name'),
            'email' => trans('backoffice::auth.email'),
            'username' => trans('backoffice::auth.username'),
            'activated' => trans('backoffice::auth.activated'),
            'lastLogin' => trans('backoffice::auth.last_login'),
            'user_id', 'name', 'id',
        ]);

        $listing->columns()
            ->hide(['id', 'user_id', 'name'])
            ->sortable(['firstName', 'lastName', 'lastLogin', 'email', 'username']);

        $listing->addValueExtractor('firstName', function (User $user) {
            return $user->getName()->getFirstName();
        });

        $listing->addValueExtractor('lastName', function (User $user) {
            return $user->getName()->getLastName();
        });

        $listing->addValueExtractor('lastLogin', function (User $user) {
            return $user->getLastLogin() ? $user->getLastLogin()->format(trans('backoffice::default.datetime_format')) : '';
        });

        $listing->addValueExtractor('id', function (User $user) {
            return $user->getUserId();
        });

        return $listing;
    }

    private function buildFilters(Listing $list): void
    {
        $filters = $list->filters();

        $filters->text('email', trans('backoffice::auth.email'), ['class' => 'form-control']);
        $filters->text('username', trans('backoffice::auth.username'), ['class' => 'form-control']);
        $filters->text('firstName', trans('backoffice::auth.first_name'), ['class' => 'form-control']);
        $filters->text('lastName', trans('backoffice::auth.last_name'), ['class' => 'form-control']);
        $filters->boolean('activated', trans('backoffice::auth.activated'), ['class' => 'form-control']);
    }

    private function buildListActions(Listing $list, Request $request): void
    {
        $actions = backoffice()->actions();

        try {
            $actions->link(
                url()->to(UserCreateHandler::route()),
                fa('plus') . ' ' . trans('backoffice::default.new', ['model' => trans('backoffice::auth.user')]),
                [
                    'class' => 'btn btn-primary',
                ]
            );
        } catch (SecurityException $e) { /* Do nothing */
        }
        try {
            $actions->link(
                url()->to(UserExportHandler::route($request->all())),
                fa('file-excel-o') . ' ' . trans('backoffice::default.export'),
                [
                    'class' => 'btn btn-success',
                ]
            );
        } catch (SecurityException $e) { /* Do nothing */
        }

        $list->setActions($actions);

        $rowActions = backoffice()->actions();

        $rowActions->link(
            function (Collection $row) {
                try {
                    return url()->to(UserShowHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('eye'),
            [
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => trans('backoffice::default.show'),
            ]
        );

        $rowActions->link(
            function (Collection $row) {
                try {
                    return url()->to(UserEditHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('edit'),
            [
                'class' => 'text-success',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => trans('backoffice::default.edit'),
            ]
        );

        $rowActions->form(
            function (Collection $row) {
                try {
                    return url()->to(UserDeleteHandler::route($row->get('id')));
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

        $rowActions->form(
            function (Collection $row) {
                try {
                    return url()->to(UserResetPasswordHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('unlock-alt'),
            Request::METHOD_POST,
            [
                'class' => 'text-warning',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-confirm' => trans('backoffice::auth.reset-password.confirm'),
                'title' => trans('backoffice::auth.reset-password.title'),
            ]
        );

        $rowActions->form(
            function (Collection $row) {
                if ($row['activated']) {
                    return false;
                }

                try {
                    return url()->to(UserResendActivationHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('reply-all'),
            Request::METHOD_POST,
            [
                'class' => 'text-primary',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-confirm' => trans('backoffice::auth.activation.confirm'),
                'title' => trans('backoffice::auth.activation.title'),
            ]
        );

        $list->setRowActions($rowActions);
    }

    private function getData(Request $request)
    {
        /** @var DoctrineUserRepository $users */
        $users = security()->users();

        $filters = $request->all(['email', 'firstName', 'lastName', 'activated', 'username']);

        $filters = array_filter($filters, function ($field) {
            return $field !== null && $field !== '';
        });

        if (array_key_exists('activated', $filters)) {
            $filters['activated'] = $filters['activated'] == 'true';
        }

        $paginationData = $this->getSorting($request);

        return $users->search(
            $filters,
            $this->convertSorting($paginationData),
            $paginationData->getLimit(),
            $paginationData->getOffset()
        );
    }

    private function getSorting(Request $request): PaginationData
    {
        $paginationData = $this->paginationBackofficeData($request);

        if ($paginationData->getSorting()->isEmpty()) {
            $paginationData->addSort('firstName');
            $paginationData->addSort('lastName');
        }

        return $paginationData;
    }

    /*
     * This is only needed when using any of the digbang/backoffice package repositories
     */
    private function convertSorting(PaginationData $paginationData): array
    {
        $sortings = [
            'firstName' => 'u.name.firstName',
            'lastName' => 'u.name.lastName',
            'lastLogin' => 'u.lastLogin',
            'email' => 'u.email.address',
            'username' => 'u.username',
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
