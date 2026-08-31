<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @phan-file-suppress PhanRedefineClass
 * @phan-file-suppress PhanRedefinedUsedTrait
 * @phan-file-suppress PhanUndeclaredMethod
 */

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\VarDumper;

use Symfony\Component\VarDumper\Cloner\Cursor;
use Symfony\Component\VarDumper\Dumper\CliDumper;

require_once __DIR__.'/DumperBase.php';

$method = new \ReflectionMethod(CliDumper::class, 'enterHash');
$typed = $method->getParameters()[1]->hasType();
$returnTyped = $method->hasReturnType();
if ($typed) {
    if ($returnTyped) {
        trait DumperEnterHashShim
        {
            public function enterHash(Cursor $cursor, int $type, $class, bool $hasChild): void
            {
                $this->doEnterHash($cursor, $type, $class, $hasChild);
            }
        }
    } else {
        trait DumperEnterHashShim
        {
            public function enterHash(Cursor $cursor, int $type, $class, bool $hasChild)
            {
                $this->doEnterHash($cursor, $type, $class, $hasChild);
            }
        }
    }
} elseif ($returnTyped) {
    trait DumperEnterHashShim
    {
        public function enterHash(Cursor $cursor, $type, $class, $hasChild): void
        {
            $this->doEnterHash($cursor, $type, $class, $hasChild);
        }
    }
} else {
    trait DumperEnterHashShim
    {
        public function enterHash(Cursor $cursor, $type, $class, $hasChild)
        {
            $this->doEnterHash($cursor, $type, $class, $hasChild);
        }
    }
}

$method = new \ReflectionMethod(CliDumper::class, 'dumpString');
$typed = $method->getParameters()[1]->hasType();
$returnTyped = $method->hasReturnType();
if ($typed) {
    if ($returnTyped) {
        trait DumperDumpStringShim
        {
            public function dumpString(Cursor $cursor, string $str, bool $bin, int $cut): void
            {
                $this->doDumpString($cursor, $str, $bin, $cut);
            }
        }
    } else {
        trait DumperDumpStringShim
        {
            public function dumpString(Cursor $cursor, string $str, bool $bin, int $cut)
            {
                $this->doDumpString($cursor, $str, $bin, $cut);
            }
        }
    }
} elseif ($returnTyped) {
    trait DumperDumpStringShim
    {
        public function dumpString(Cursor $cursor, $str, $bin, $cut): void
        {
            $this->doDumpString($cursor, $str, $bin, $cut);
        }
    }
} else {
    trait DumperDumpStringShim
    {
        public function dumpString(Cursor $cursor, $str, $bin, $cut)
        {
            $this->doDumpString($cursor, $str, $bin, $cut);
        }
    }
}

$method = new \ReflectionMethod(CliDumper::class, 'style');
$typed = $method->getParameters()[0]->hasType();
$returnTyped = $method->hasReturnType();
if ($typed) {
    if ($returnTyped) {
        trait DumperStyleShim
        {
            protected function style(string $style, string $value, array $attr = []): string
            {
                return $this->doStyle($style, $value, $attr);
            }
        }
    } else {
        trait DumperStyleShim
        {
            protected function style(string $style, string $value, array $attr = [])
            {
                return $this->doStyle($style, $value, $attr);
            }
        }
    }
} elseif ($returnTyped) {
    trait DumperStyleShim
    {
        protected function style($style, $value, $attr = []): string
        {
            return $this->doStyle($style, $value, $attr);
        }
    }
} else {
    trait DumperStyleShim
    {
        protected function style($style, $value, $attr = [])
        {
            return $this->doStyle($style, $value, $attr);
        }
    }
}

$method = new \ReflectionMethod(CliDumper::class, 'dumpLine');
$typed = $method->getParameters()[0]->hasType();
$returnTyped = $method->hasReturnType();
if ($typed) {
    if ($returnTyped) {
        trait DumperDumpLineShim
        {
            protected function dumpLine(int $depth, bool $endOfValue = false): void
            {
                $this->doDumpLine($depth, $endOfValue);
            }
        }
    } else {
        trait DumperDumpLineShim
        {
            protected function dumpLine(int $depth, bool $endOfValue = false)
            {
                $this->doDumpLine($depth, $endOfValue);
            }
        }
    }
} elseif ($returnTyped) {
    trait DumperDumpLineShim
    {
        protected function dumpLine($depth, $endOfValue = false): void
        {
            $this->doDumpLine($depth, $endOfValue);
        }
    }
} else {
    trait DumperDumpLineShim
    {
        protected function dumpLine($depth, $endOfValue = false)
        {
            $this->doDumpLine($depth, $endOfValue);
        }
    }
}

$method = new \ReflectionMethod(CliDumper::class, 'dumpKey');
$typed = $method->getParameters()[0]->hasType();
$returnTyped = $method->hasReturnType();
if ($typed) {
    if ($returnTyped) {
        trait DumperDumpKeyShim
        {
            protected function dumpKey(Cursor $cursor): void
            {
                $this->doDumpKey($cursor);
            }
        }
    } else {
        trait DumperDumpKeyShim
        {
            protected function dumpKey(Cursor $cursor)
            {
                $this->doDumpKey($cursor);
            }
        }
    }
} elseif ($returnTyped) {
    trait DumperDumpKeyShim
    {
        protected function dumpKey($cursor): void
        {
            $this->doDumpKey($cursor);
        }
    }
} else {
    trait DumperDumpKeyShim
    {
        protected function dumpKey($cursor)
        {
            $this->doDumpKey($cursor);
        }
    }
}

class Dumper extends DumperBase
{
    use DumperDumpKeyShim;
    use DumperDumpLineShim;
    use DumperDumpStringShim;
    use DumperEnterHashShim;
    use DumperStyleShim;
}
