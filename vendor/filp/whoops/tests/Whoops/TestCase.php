<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops;
use Whoops\Run;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Run
     */
    protected function getRunInstance()
    {
        $run = new Run;
        $run->allowQuit(false);

        return $run;
    }
}
