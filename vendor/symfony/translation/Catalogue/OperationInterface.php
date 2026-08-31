<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Catalogue;

use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Represents an operation on catalogue(s).
 *
 * An instance of this interface performs an operation on one or more catalogues and
 * stores intermediate and final results of the operation.
 *
 * The first catalogue in its argument(s) is called the 'source catalogue' or 'source' and
 * the following results are stored:
 *
 * Messages: also called 'all', are valid messages for the given domain after the operation is performed.
 *
 * New Messages: also called 'new' (new = all ∖ source = {x: x ∈ all ∧ x ∉ source}).
 *
 * Obsolete Messages: also called 'obsolete' (obsolete = source ∖ all = {x: x ∈ source ∧ x ∉ all}).
 *
 * Result: also called 'result', is the resulting catalogue for the given domain that holds the same messages as 'all'.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
interface OperationInterface
{
    /**
     * Returns domains affected by operation.
     */
    public function getDomains(): array;

    /**
     * Returns all valid messages ('all') after operation.
     */
    public function getMessages(string $domain): array;

    /**
     * Returns new messages ('new') after operation.
     */
    public function getNewMessages(string $domain): array;

    /**
     * Returns obsolete messages ('obsolete') after operation.
     */
    public function getObsoleteMessages(string $domain): array;

    /**
     * Returns resulting catalogue ('result').
     */
    public function getResult(): MessageCatalogueInterface;
}
