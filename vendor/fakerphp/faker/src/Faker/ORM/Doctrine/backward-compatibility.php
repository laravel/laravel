<?php

declare(strict_types=1);

if (!class_exists('Doctrine\Common\Persistence\Mapping\ClassMetadata')) {
    class_alias(\Doctrine\Persistence\Mapping\ClassMetadata::class, 'Doctrine\Common\Persistence\Mapping\ClassMetadata');
}

if (!class_exists('Doctrine\Common\Persistence\ObjectManager')) {
    class_alias(\Doctrine\Persistence\ObjectManager::class, 'Doctrine\Common\Persistence\ObjectManager');
}
