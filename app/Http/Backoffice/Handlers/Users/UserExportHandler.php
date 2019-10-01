<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\PaginationRequest;
use App\Http\Util\RouteDefiner;
use App\Infrastructure\Util\DataExporter;
use App\Infrastructure\Util\PaginationData;
use Digbang\Security\Users\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class UserExportHandler extends Handler implements RouteDefiner
{
    use PaginationRequest;

    public function __invoke(Request $request, DataExporter $exporter)
    {
        $items = new Collection($this->getData($request));
        $items = $items->map(function (User $user) {
            return [
                trans('backoffice::auth.first_name') => $user->getName()->getFirstName(),
                trans('backoffice::auth.last_name') => $user->getName()->getLastName(),
                trans('backoffice::auth.email') => $user->getEmail(),
                trans('backoffice::auth.username') => $user->getUsername(),
                trans('backoffice::auth.activated') => $user->isActivated() ? $user->getActivatedAt()->format(trans('backoffice::default.datetime_format')) : '',
                trans('backoffice::auth.last_login') => $user->getLastLogin() ? $user->getLastLogin()->format(trans('backoffice::default.datetime_format')) : '',
            ];
        })->toArray();

        $filename = (new \DateTime())->format('Ymd') . ' - ' . trans('backoffice::auth.users');
        $exporter
            ->setSheetName(trans('backoffice::auth.users'))
            ->xls(
                $items,
                $filename
            );
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/export", [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_EXPORT,
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
        /** @var \Digbang\Backoffice\Repositories\DoctrineUserRepository $users */
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

    private function getSorting(Request $request)
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
