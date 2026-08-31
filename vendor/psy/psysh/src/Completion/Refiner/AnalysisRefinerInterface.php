<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion\Refiner;

use Psy\Completion\AnalysisResult;

/**
 * Narrows parser output into a more useful completion lane.
 *
 * Refiners handle context decisions that depend on more than the parser's
 * immediate syntactic view of the input.
 */
interface AnalysisRefinerInterface
{
    /**
     * Refine the analysis result before sources are queried.
     */
    public function refine(AnalysisResult $analysis): AnalysisResult;
}
