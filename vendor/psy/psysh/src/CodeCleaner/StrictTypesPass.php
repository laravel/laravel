<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\CodeCleaner;

use PhpParser\Node;
use PhpParser\Node\DeclareItem;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use Psy\Exception\FatalErrorException;

/**
 * Provide implicit strict types declarations for for subsequent execution.
 *
 * The strict types pass remembers the last strict types declaration:
 *
 *     declare(strict_types=1);
 *
 * ... which it then applies implicitly to all future evaluated code, until it
 * is replaced by a new declaration.
 */
class StrictTypesPass extends CodeCleanerPass
{
    const EXCEPTION_MESSAGE = 'strict_types declaration must have 0 or 1 as its value';

    private bool $strictTypes;

    /**
     * @param bool $strictTypes enforce strict types by default
     */
    public function __construct(bool $strictTypes = false)
    {
        $this->strictTypes = $strictTypes;
    }

    /**
     * If this is a standalone strict types declaration, remember it for later.
     *
     * Otherwise, apply remembered strict types declaration to to the code until
     * a new declaration is encountered.
     *
     * @throws FatalErrorException if an invalid `strict_types` declaration is found
     *
     * @param array $nodes
     *
     * @return Node[]|null Array of nodes
     */
    public function beforeTraverse(array $nodes)
    {
        $prependStrictTypes = $this->strictTypes;

        foreach ($nodes as $node) {
            if ($node instanceof Declare_) {
                foreach ($node->declares as $declare) {
                    if ($declare->key->toString() === 'strict_types') {
                        $value = $declare->value;
                        // @todo Remove LNumber once we drop support for PHP-Parser 4.x
                        if ((!$value instanceof LNumber && !$value instanceof Int_) || ($value->value !== 0 && $value->value !== 1)) {
                            throw new FatalErrorException(self::EXCEPTION_MESSAGE, 0, \E_ERROR, null, $node->getStartLine());
                        }

                        $this->strictTypes = $value->value === 1;
                    }
                }
            }
        }

        if ($prependStrictTypes) {
            $first = \reset($nodes);
            if (!$first instanceof Declare_) {
                // @todo Switch to PhpParser\Node\DeclareItem once we drop support for PHP-Parser 4.x
                // @todo Remove LNumber once we drop support for PHP-Parser 4.x
                $arg = \class_exists('PhpParser\Node\Scalar\Int_') ? new Int_(1) : new LNumber(1);
                $declareItem = \class_exists('PhpParser\Node\DeclareItem') ?
                    new DeclareItem('strict_types', $arg) :
                    new DeclareDeclare('strict_types', $arg);
                $declare = new Declare_([$declareItem]);
                \array_unshift($nodes, $declare);
            }
        }

        return $nodes;
    }
}
