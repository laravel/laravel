<?php

namespace App\Infrastructure\Doctrine\Mappings;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use ProjectName\Entities\User;
use ProjectName\Immutables\Name;

class UserMapping extends EntityMapping
{
    public function mapFor(): string
    {
        return User::class;
    }

    public function map(Fluent $builder): void
    {
        $builder->bigIncrements('id')->primary();
        $builder->embed(Name::class);
        $builder->text('email');
        $builder->text('password');
        $builder->timestamps('createdAt', 'updatedAt', 'chronosDateTime');
        $builder->softDelete('deletedAt', 'chronosDateTime');

        $builder->unique(['email', 'deleted_at'])->name('users_email_unique');
    }
}
