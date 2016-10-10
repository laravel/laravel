<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Util;

use Symfony\Component\Security\Core\Util\SecureRandom;

class SecureRandomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * T1: Monobit test
     *
     * @dataProvider getSecureRandoms
     */
    public function testMonobit($secureRandom)
    {
        $nbOnBits = substr_count($this->getBitSequence($secureRandom, 20000), '1');
        $this->assertTrue($nbOnBits > 9654 && $nbOnBits < 10346, 'Monobit test failed, number of turned on bits: '.$nbOnBits);
    }

    /**
     * T2: Chi-square test with 15 degrees of freedom (chi-Quadrat-Anpassungstest)
     *
     * @dataProvider getSecureRandoms
     */
    public function testPoker($secureRandom)
    {
        $b = $this->getBitSequence($secureRandom, 20000);
        $c = array();
        for ($i = 0; $i <= 15; $i++) {
            $c[$i] = 0;
        }

        for ($j = 1; $j <= 5000; $j++) {
            $k = 4 * $j - 1;
            $c[8 * $b[$k - 3] + 4 * $b[$k - 2] + 2 * $b[$k - 1] + $b[$k]] += 1;
        }

        $f = 0;
        for ($i = 0; $i <= 15; $i++) {
            $f += $c[$i] * $c[$i];
        }

        $Y = 16/5000 * $f - 5000;

        $this->assertTrue($Y > 1.03 && $Y < 57.4, 'Poker test failed, Y = '.$Y);
    }

    /**
     * Run test
     *
     * @dataProvider getSecureRandoms
     */
    public function testRun($secureRandom)
    {
        $b = $this->getBitSequence($secureRandom, 20000);

        $runs = array();
        for ($i = 1; $i <= 6; $i++) {
            $runs[$i] = 0;
        }

        $addRun = function ($run) use (&$runs) {
            if ($run > 6) {
                $run = 6;
            }

            $runs[$run] += 1;
        };

        $currentRun = 0;
        $lastBit = null;
        for ($i = 0; $i < 20000; $i++) {
            if ($lastBit === $b[$i]) {
                $currentRun += 1;
            } else {
                if ($currentRun > 0) {
                    $addRun($currentRun);
                }

                $lastBit = $b[$i];
                $currentRun = 0;
            }
        }
        if ($currentRun > 0) {
            $addRun($currentRun);
        }

        $this->assertTrue($runs[1] > 2267 && $runs[1] < 2733, 'Runs of length 1 outside of defined interval: '.$runs[1]);
        $this->assertTrue($runs[2] > 1079 && $runs[2] < 1421, 'Runs of length 2 outside of defined interval: '.$runs[2]);
        $this->assertTrue($runs[3] > 502 && $runs[3] < 748, 'Runs of length 3 outside of defined interval: '.$runs[3]);
        $this->assertTrue($runs[4] > 233 && $runs[4] < 402, 'Runs of length 4 outside of defined interval: '.$runs[4]);
        $this->assertTrue($runs[5] > 90 && $runs[5] < 223, 'Runs of length 5 outside of defined interval: '.$runs[5]);
        $this->assertTrue($runs[6] > 90 && $runs[6] < 233, 'Runs of length 6 outside of defined interval: '.$runs[6]);
    }

    /**
     * Long-run test
     *
     * @dataProvider getSecureRandoms
     */
    public function testLongRun($secureRandom)
    {
        $b = $this->getBitSequence($secureRandom, 20000);

        $longestRun = $currentRun = 0;
        $lastBit = null;
        for ($i = 0; $i < 20000; $i++) {
            if ($lastBit === $b[$i]) {
                $currentRun += 1;
            } else {
                if ($currentRun > $longestRun) {
                    $longestRun = $currentRun;
                }
                $lastBit = $b[$i];
                $currentRun = 0;
            }
        }
        if ($currentRun > $longestRun) {
            $longestRun = $currentRun;
        }

        $this->assertTrue($longestRun < 34, 'Failed longest run test: '.$longestRun);
    }

    /**
     * Serial Correlation (Autokorrelationstest)
     *
     * @dataProvider getSecureRandoms
     */
    public function testSerialCorrelation($secureRandom)
    {
        $shift = rand(1, 5000);
        $b = $this->getBitSequence($secureRandom, 20000);

        $Z = 0;
        for ($i = 0; $i < 5000; $i++) {
            $Z += $b[$i] === $b[$i + $shift] ? 1 : 0;
        }

        $this->assertTrue($Z > 2326 && $Z < 2674, 'Failed serial correlation test: '.$Z);
    }

    public function getSecureRandoms()
    {
        $secureRandoms = array();

        // only add if openssl is indeed present
        $secureRandom = new SecureRandom();
        if ($this->hasOpenSsl($secureRandom)) {
            $secureRandoms[] = array($secureRandom);
        }

        // no-openssl with custom seed provider
        $secureRandom = new SecureRandom(sys_get_temp_dir().'/_sf2.seed');
        $this->disableOpenSsl($secureRandom);
        $secureRandoms[] = array($secureRandom);

        return $secureRandoms;
    }

    protected function disableOpenSsl($secureRandom)
    {
        $ref = new \ReflectionProperty($secureRandom, 'useOpenSsl');
        $ref->setAccessible(true);
        $ref->setValue($secureRandom, false);
        $ref->setAccessible(false);
    }

    protected function hasOpenSsl($secureRandom)
    {
        $ref = new \ReflectionProperty($secureRandom, 'useOpenSsl');
        $ref->setAccessible(true);

        $ret = $ref->getValue($secureRandom);

        $ref->setAccessible(false);

        return $ret;
    }

    private function getBitSequence($secureRandom, $length)
    {
        $bitSequence = '';
        for ($i = 0; $i < $length; $i += 40) {
            $value = unpack('H*', $secureRandom->nextBytes(5));
            $value = str_pad(base_convert($value[1], 16, 2), 40, '0', STR_PAD_LEFT);
            $bitSequence .= $value;
        }

        return substr($bitSequence, 0, $length);
    }
}
