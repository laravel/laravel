<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\VarDumper;

/**
 * Presenter injects itself as a dependency to all objects which
 * implement PresenterAware.
 */
interface PresenterAware
{
    /**
     * Set a reference to the Presenter.
     */
    public function setPresenter(Presenter $presenter);
}
