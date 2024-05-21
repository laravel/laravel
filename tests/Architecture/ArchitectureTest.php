<?php

declare(strict_types=1);

namespace Architecture;

arch('globals')
    ->expect('Lightit')
    ->toUseStrictTypes()
    ->not->toUse(['dd', 'dump', 'die', 'var_dump', 'print_r']);

arch('dtos')
    ->expect('Lightit\\*\\Domain\\DataTransferObjects')
    ->toExtendNothing()
    ->toBeReadonly();

arch('controllers')
    ->expect('Lightit\\*\\App\\Controllers')
    ->toBeInvokable()
    ->toHaveSuffix('Controller');

arch('requests')
    ->expect('Lightit\\*\\App\\Requests')
    ->toHaveSuffix('Request');

arch('resources')
    ->expect('Lightit\\*\\App\\Resources')
    ->toHaveSuffix('Resource');

arch('notifications')
    ->expect('Lightit\\*\\App\\Notifications')
    ->toHaveSuffix('Notification');

arch('no env fuera de config')
    ->expect('Lightit')
    ->not->toUse('env');

arch('config typesafe')
    ->expect('Lightit')
    ->not->toUse('config');

arch('actions')
    ->expect('Lightit\\*\\Domain\\Actions')
    ->toHaveSuffix('Action')
    ->toHaveMethod('execute')
    ->not->toHavePublicMethodsBesides(['execute']);

arch('models')
    ->expect('Lightit\\*\\Domain\\Models')
    ->not->toUseTrait('Illuminate\Database\Eloquent\Factories\HasFactory\HasFactory');
