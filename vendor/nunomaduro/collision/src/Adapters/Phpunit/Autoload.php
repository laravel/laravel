<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Adapters\Phpunit\Subscribers\EnsurePrinterIsRegisteredSubscriber;
use PHPUnit\Runner\Version;

if (class_exists(Version::class) && (int) Version::series() >= 10) {
    EnsurePrinterIsRegisteredSubscriber::register();
}
