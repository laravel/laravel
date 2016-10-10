<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests;

use Symfony\Component\Translation\PluralizationRules;

/**
 * Test should cover all languages mentioned on http://translate.sourceforge.net/wiki/l10n/pluralforms
 * and Plural forms mentioned on http://www.gnu.org/software/gettext/manual/gettext.html#Plural-forms
 *
 * See also https://developer.mozilla.org/en/Localization_and_Plurals which mentions 15 rules having a maximum of 6 forms.
 * The mozilla code is also interesting to check for.
 *
 * As mentioned by chx http://drupal.org/node/1273968 we can cover all by testing number from 0 to 199
 *
 * The goal to cover all languages is to far fetched so this test case is smaller.
 *
 * @author Clemens Tolboom clemens@build2be.nl
 */
class PluralizationRulesTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * We test failed langcode here.
     *
     * TODO: The languages mentioned in the data provide need to get fixed somehow within PluralizationRules.
     *
     * @dataProvider failingLangcodes
     */
    public function testFailedLangcodes($nplural, $langCodes)
    {
        $matrix = $this->generateTestData($nplural, $langCodes);
        $this->validateMatrix($nplural, $matrix, false);
    }

    /**
     * @dataProvider successLangcodes
     */
    public function testLangcodes($nplural, $langCodes)
    {
        $matrix = $this->generateTestData($nplural, $langCodes);
        $this->validateMatrix($nplural, $matrix);
    }

    /**
     * This array should contain all currently known langcodes.
     *
     * As it is impossible to have this ever complete we should try as hard as possible to have it almost complete.
     *
     * @return type
     */
    public function successLangcodes()
    {
        return array(
        array('1' , array('ay','bo', 'cgg','dz','id', 'ja', 'jbo', 'ka','kk','km','ko','ky')),
        array('2' , array('nl', 'fr', 'en', 'de', 'de_GE')),
        array('3' , array('be','bs','cs','hr')),
        array('4' , array('cy','mt', 'sl')),
        array('5' , array()),
        array('6' , array('ar')),
      );
    }

    /**
     * This array should be at least empty within the near future.
     *
     * This both depends on a complete list trying to add above as understanding
     * the plural rules of the current failing languages.
     *
     * @return array with nplural together with langcodes
     */
    public function failingLangcodes()
    {
        return array(
        array('1' , array('fa')),
        array('2' , array('jbo')),
        array('3' , array('cbs')),
        array('4' , array('gd','kw')),
        array('5' , array('ga')),
        array('6' , array()),
      );
    }

    /**
     * We validate only on the plural coverage. Thus the real rules is not tested.
     *
     * @param string  $nplural       plural expected
     * @param array   $matrix        containing langcodes and their plural index values.
     * @param bool    $expectSuccess
     */
    protected function validateMatrix($nplural, $matrix, $expectSuccess = true)
    {
        foreach ($matrix as $langCode => $data) {
            $indexes = array_flip($data);
            if ($expectSuccess) {
                $this->assertEquals($nplural, count($indexes), "Langcode '$langCode' has '$nplural' plural forms.");
            } else {
                $this->assertNotEquals((int) $nplural, count($indexes), "Langcode '$langCode' has '$nplural' plural forms.");
            }
        }
    }

    protected function generateTestData($plural, $langCodes)
    {
        $matrix = array();
        foreach ($langCodes as $langCode) {
            for ($count = 0; $count<200; $count++) {
                $plural = PluralizationRules::get($count, $langCode);
                $matrix[$langCode][$count] = $plural;
            }
        }

        return $matrix;
    }
}
