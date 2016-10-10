<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

/**
 * Normalizer plugs Patchwork\PHP\Shim\Normalizer as a PHP implementation
 * of intl's Normalizer when the intl extension in not enabled.
 */
class Normalizer extends Patchwork\PHP\Shim\Normalizer
{
}
