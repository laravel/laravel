<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Examples;

use Orchid\Screen\Layouts\Chart;

class ChartPercentageExample extends Chart
{
    /**
     * @var string
     */
    protected $title = 'Percentage Chart';

    /**
     * @var int
     */
    protected $height = 160;

    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'percentage';

    /**
     * @var string
     */
    protected $target = 'charts';
}
