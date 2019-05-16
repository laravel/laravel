<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserCriteriaRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use App\Infrastructure\Util\DataExporter;
use Digbang\Security\Users\User;
use Digbang\Utils\Sorting;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use ProjectName\Repositories\Criteria\Users\UserSorting;

class UserExportHandler extends Handler implements RouteDefiner
{
    public function __invoke(UserCriteriaRequest $request, DataExporter $exporter)
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

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/export", [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_EXPORT,
            ])
            ->name(static::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(array $filter): string
    {
        return route(static::class, $filter);
    }

    private function getData(UserCriteriaRequest $request)
    {
        /** @var \Digbang\Backoffice\Repositories\DoctrineUserRepository $users */
        $users = security()->users();

        $filters = $request->getFilter()->values();

        $filters = array_filter($filters, function ($field) {
            return $field !== null && $field !== '';
        });

        if (array_key_exists('activated', $filters)) {
            $filters['activated'] = $filters['activated'] == 'true';
        }

        $sorting = $this->convertSorting($request->getSorting());

        return $users->search($filters, $sorting, null, 0);
    }

    /*
     * This is only needed when using any of the digbang/backoffice package repositories
     */
    private function convertSorting(Sorting $userSorting): array
    {
        $sortings = [
            UserSorting::FIRST_NAME => 'u.name.firstName',
            UserSorting::LAST_NAME => 'u.name.lastName',
            UserSorting::EMAIL => 'u.email.address',
            UserSorting::USERNAME => 'u.username',
            UserSorting::LAST_LOGIN => 'u.lastLogin',
        ];

        $selectedSorts = $userSorting->get(array_keys($sortings));
        if (empty($selectedSorts)) {
            $selectedSorts = [
                UserSorting::FIRST_NAME => 'ASC',
                UserSorting::LAST_NAME => 'ASC',
            ];
        }

        $orderBy = [];
        foreach ($selectedSorts as $key => $sense) {
            $key = $sortings[$key];
            $orderBy[$key] = $sense;
        }

        return $orderBy;
    }
}
