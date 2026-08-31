<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Contracts\Translation\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

/**
 * Test should cover all languages mentioned on http://translate.sourceforge.net/wiki/l10n/pluralforms
 * and Plural forms mentioned on http://www.gnu.org/software/gettext/manual/gettext.html#Plural-forms.
 *
 * See also https://developer.mozilla.org/en/Localization_and_Plurals which mentions 15 rules having a maximum of 6 forms.
 * The mozilla code is also interesting to check for.
 *
 * As mentioned by chx http://drupal.org/node/1273968 we can cover all by testing number from 0 to 199
 *
 * The goal to cover all languages is too far fetched so this test case is smaller.
 *
 * @author Clemens Tolboom clemens@build2be.nl
 */
class TranslatorTest extends TestCase
{
    private string $defaultLocale;

    protected function setUp(): void
    {
        $this->defaultLocale = \Locale::getDefault();
        \Locale::setDefault('en');
    }

    protected function tearDown(): void
    {
        \Locale::setDefault($this->defaultLocale);
    }

    public function getTranslator(): TranslatorInterface
    {
        return new class implements TranslatorInterface {
            use TranslatorTrait;
        };
    }

    /**
     * @dataProvider getTransTests
     */
    #[DataProvider('getTransTests')]
    public function testTrans($expected, $id, $parameters)
    {
        $translator = $this->getTranslator();

        $this->assertEquals($expected, $translator->trans($id, $parameters));
    }

    /**
     * @dataProvider getTransChoiceTests
     */
    #[DataProvider('getTransChoiceTests')]
    public function testTransChoiceWithExplicitLocale($expected, $id, $number)
    {
        $translator = $this->getTranslator();

        $this->assertEquals($expected, $translator->trans($id, ['%count%' => $number]));
    }

    /**
     * @requires extension intl
     *
     * @dataProvider getTransChoiceTests
     */
    #[DataProvider('getTransChoiceTests')]
    #[RequiresPhpExtension('intl')]
    public function testTransChoiceWithDefaultLocale($expected, $id, $number)
    {
        $translator = $this->getTranslator();

        $this->assertEquals($expected, $translator->trans($id, ['%count%' => $number]));
    }

    /**
     * @dataProvider getTransChoiceTests
     */
    #[DataProvider('getTransChoiceTests')]
    public function testTransChoiceWithEnUsPosix($expected, $id, $number)
    {
        $translator = $this->getTranslator();
        $translator->setLocale('en_US_POSIX');

        $this->assertEquals($expected, $translator->trans($id, ['%count%' => $number]));
    }

    public function testGetSetLocale()
    {
        $translator = $this->getTranslator();

        $this->assertEquals('en', $translator->getLocale());
    }

    /**
     * @requires extension intl
     */
    #[RequiresPhpExtension('intl')]
    public function testGetLocaleReturnsDefaultLocaleIfNotSet()
    {
        $translator = $this->getTranslator();

        \Locale::setDefault('pt_BR');
        $this->assertEquals('pt_BR', $translator->getLocale());

        \Locale::setDefault('en');
        $this->assertEquals('en', $translator->getLocale());
    }

    public static function getTransTests()
    {
        yield ['Symfony is great!', 'Symfony is great!', []];
        yield ['Symfony is awesome!', 'Symfony is %what%!', ['%what%' => 'awesome']];

        if (class_exists(TranslatableMessage::class)) {
            yield ['He said "Symfony is awesome!".', 'He said "%what%".', ['%what%' => new TranslatableMessage('Symfony is %what%!', ['%what%' => 'awesome'])]];
        }
    }

    public static function getTransChoiceTests()
    {
        return [
            ['There are no apples', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 0],
            ['There is one apple', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 1],
            ['There are 10 apples', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 10],
            ['There are 0 apples', 'There is 1 apple|There are %count% apples', 0],
            ['There is 1 apple', 'There is 1 apple|There are %count% apples', 1],
            ['There are 10 apples', 'There is 1 apple|There are %count% apples', 10],
            // custom validation messages may be coded with a fixed value
            ['There are 2 apples', 'There are 2 apples', 2],
        ];
    }

    /**
     * @dataProvider getInterval
     */
    #[DataProvider('getInterval')]
    public function testInterval($expected, $number, $interval)
    {
        $translator = $this->getTranslator();

        $this->assertEquals($expected, $translator->trans($interval.' foo|[1,Inf[ bar', ['%count%' => $number]));
    }

    public static function getInterval()
    {
        return [
            ['foo', 3, '{1,2, 3 ,4}'],
            ['bar', 10, '{1,2, 3 ,4}'],
            ['bar', 3, '[1,2]'],
            ['foo', 1, '[1,2]'],
            ['foo', 2, '[1,2]'],
            ['bar', 1, ']1,2['],
            ['bar', 2, ']1,2['],
            ['foo', log(0), '[-Inf,2['],
            ['foo', -log(0), '[-2,+Inf]'],
        ];
    }

    /**
     * @dataProvider getChooseTests
     */
    #[DataProvider('getChooseTests')]
    public function testChoose($expected, $id, $number, $locale = null)
    {
        $translator = $this->getTranslator();

        $this->assertEquals($expected, $translator->trans($id, ['%count%' => $number], null, $locale));
    }

    public function testReturnMessageIfExactlyOneStandardRuleIsGiven()
    {
        $translator = $this->getTranslator();

        $this->assertEquals('There are two apples', $translator->trans('There are two apples', ['%count%' => 2]));
    }

    /**
     * @dataProvider getNonMatchingMessages
     */
    #[DataProvider('getNonMatchingMessages')]
    public function testThrowExceptionIfMatchingMessageCannotBeFound($id, $number)
    {
        $translator = $this->getTranslator();

        $this->expectException(\InvalidArgumentException::class);

        $translator->trans($id, ['%count%' => $number]);
    }

    public static function getNonMatchingMessages()
    {
        return [
            ['{0} There are no apples|{1} There is one apple', 2],
            ['{1} There is one apple|]1,Inf] There are %count% apples', 0],
            ['{1} There is one apple|]2,Inf] There are %count% apples', 2],
            ['{0} There are no apples|There is one apple', 2],
        ];
    }

    public static function getChooseTests()
    {
        return [
            ['There are no apples', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 0],
            ['There are no apples', '{0}     There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 0],
            ['There are no apples', '{0}There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 0],

            ['There is one apple', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 1],

            ['There are 10 apples', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 10],
            ['There are 10 apples', '{0} There are no apples|{1} There is one apple|]1,Inf]There are %count% apples', 10],
            ['There are 10 apples', '{0} There are no apples|{1} There is one apple|]1,Inf]     There are %count% apples', 10],

            ['There are 0 apples', 'There is one apple|There are %count% apples', 0],
            ['There is one apple', 'There is one apple|There are %count% apples', 1],
            ['There are 10 apples', 'There is one apple|There are %count% apples', 10],

            ['There are 0 apples', 'one: There is one apple|more: There are %count% apples', 0],
            ['There is one apple', 'one: There is one apple|more: There are %count% apples', 1],
            ['There are 10 apples', 'one: There is one apple|more: There are %count% apples', 10],

            ['There are no apples', '{0} There are no apples|one: There is one apple|more: There are %count% apples', 0],
            ['There is one apple', '{0} There are no apples|one: There is one apple|more: There are %count% apples', 1],
            ['There are 10 apples', '{0} There are no apples|one: There is one apple|more: There are %count% apples', 10],

            ['', '{0}|{1} There is one apple|]1,Inf] There are %count% apples', 0],
            ['', '{0} There are no apples|{1}|]1,Inf] There are %count% apples', 1],

            // Indexed only tests which are Gettext PoFile* compatible strings.
            ['There are 0 apples', 'There is one apple|There are %count% apples', 0],
            ['There is one apple', 'There is one apple|There are %count% apples', 1],
            ['There are 2 apples', 'There is one apple|There are %count% apples', 2],

            // Tests for float numbers
            ['There is almost one apple', '{0} There are no apples|]0,1[ There is almost one apple|{1} There is one apple|[1,Inf] There is more than one apple', 0.7],
            ['There is one apple', '{0} There are no apples|]0,1[There are %count% apples|{1} There is one apple|[1,Inf] There is more than one apple', 1],
            ['There is more than one apple', '{0} There are no apples|]0,1[There are %count% apples|{1} There is one apple|[1,Inf] There is more than one apple', 1.7],
            ['There are no apples', '{0} There are no apples|]0,1[There are %count% apples|{1} There is one apple|[1,Inf] There is more than one apple', 0],
            ['There are no apples', '{0} There are no apples|]0,1[There are %count% apples|{1} There is one apple|[1,Inf] There is more than one apple', 0.0],
            ['There are no apples', '{0.0} There are no apples|]0,1[There are %count% apples|{1} There is one apple|[1,Inf] There is more than one apple', 0],

            // Test texts with new-lines
            // with double-quotes and \n in id & double-quotes and actual newlines in text
            ["This is a text with a\n            new-line in it. Selector = 0.", '{0}This is a text with a
            new-line in it. Selector = 0.|{1}This is a text with a
            new-line in it. Selector = 1.|[1,Inf]This is a text with a
            new-line in it. Selector > 1.', 0],
            // with double-quotes and \n in id and single-quotes and actual newlines in text
            ["This is a text with a\n            new-line in it. Selector = 1.", '{0}This is a text with a
            new-line in it. Selector = 0.|{1}This is a text with a
            new-line in it. Selector = 1.|[1,Inf]This is a text with a
            new-line in it. Selector > 1.', 1],
            ["This is a text with a\n            new-line in it. Selector > 1.", '{0}This is a text with a
            new-line in it. Selector = 0.|{1}This is a text with a
            new-line in it. Selector = 1.|[1,Inf]This is a text with a
            new-line in it. Selector > 1.', 5],
            // with double-quotes and id split across lines
            ['This is a text with a
            new-line in it. Selector = 1.', '{0}This is a text with a
            new-line in it. Selector = 0.|{1}This is a text with a
            new-line in it. Selector = 1.|[1,Inf]This is a text with a
            new-line in it. Selector > 1.', 1],
            // with single-quotes and id split across lines
            ['This is a text with a
            new-line in it. Selector > 1.', '{0}This is a text with a
            new-line in it. Selector = 0.|{1}This is a text with a
            new-line in it. Selector = 1.|[1,Inf]This is a text with a
            new-line in it. Selector > 1.', 5],
            // with single-quotes and \n in text
            ['This is a text with a\nnew-line in it. Selector = 0.', '{0}This is a text with a\nnew-line in it. Selector = 0.|{1}This is a text with a\nnew-line in it. Selector = 1.|[1,Inf]This is a text with a\nnew-line in it. Selector > 1.', 0],
            // with double-quotes and id split across lines
            ["This is a text with a\nnew-line in it. Selector = 1.", "{0}This is a text with a\nnew-line in it. Selector = 0.|{1}This is a text with a\nnew-line in it. Selector = 1.|[1,Inf]This is a text with a\nnew-line in it. Selector > 1.", 1],
            // escape pipe
            ['This is a text with | in it. Selector = 0.', '{0}This is a text with || in it. Selector = 0.|{1}This is a text with || in it. Selector = 1.', 0],
            // Empty plural set (2 plural forms) from a .PO file
            ['', '|', 1],
            // Empty plural set (3 plural forms) from a .PO file
            ['', '||', 1],

            // Floating values
            ['1.5 liters', '%count% liter|%count% liters', 1.5],
            ['1.5 litre', '%count% litre|%count% litres', 1.5, 'fr'],

            // Negative values
            ['-1 degree', '%count% degree|%count% degrees', -1],
            ['-1 degré', '%count% degré|%count% degrés', -1],
            ['-1.5 degrees', '%count% degree|%count% degrees', -1.5],
            ['-1.5 degré', '%count% degré|%count% degrés', -1.5, 'fr'],
            ['-2 degrees', '%count% degree|%count% degrees', -2],
            ['-2 degrés', '%count% degré|%count% degrés', -2],
        ];
    }

    /**
     * @dataProvider failingLangcodes
     */
    #[DataProvider('failingLangcodes')]
    public function testFailedLangcodes($nplural, $langCodes)
    {
        $matrix = $this->generateTestData($langCodes);
        $this->validateMatrix($nplural, $matrix, false);
    }

    /**
     * @dataProvider successLangcodes
     */
    #[DataProvider('successLangcodes')]
    public function testLangcodes($nplural, $langCodes)
    {
        $matrix = $this->generateTestData($langCodes);
        $this->validateMatrix($nplural, $matrix);
    }

    /**
     * This array should contain all currently known langcodes.
     *
     * As it is impossible to have this ever complete we should try as hard as possible to have it almost complete.
     */
    public static function successLangcodes(): array
    {
        return [
            ['1', ['ay', 'bo', 'cgg', 'dz', 'id', 'ja', 'jbo', 'ka', 'kk', 'km', 'ko', 'ky']],
            ['2', ['nl', 'fr', 'en', 'de', 'de_GE', 'hy', 'hy_AM', 'en_US_POSIX']],
            ['3', ['be', 'bs', 'cs', 'hr']],
            ['4', ['cy', 'mt', 'sl']],
            ['6', ['ar']],
        ];
    }

    /**
     * This array should be at least empty within the near future.
     *
     * This both depends on a complete list trying to add above as understanding
     * the plural rules of the current failing languages.
     *
     * @return array With nplural together with langcodes
     */
    public static function failingLangcodes(): array
    {
        return [
            ['1', ['fa']],
            ['2', ['jbo']],
            ['3', ['cbs']],
            ['4', ['gd', 'kw']],
            ['5', ['ga']],
        ];
    }

    /**
     * We validate only on the plural coverage. Thus the real rules is not tested.
     *
     * @param string $nplural Plural expected
     * @param array  $matrix  Containing langcodes and their plural index values
     */
    protected function validateMatrix(string $nplural, array $matrix, bool $expectSuccess = true)
    {
        foreach ($matrix as $langCode => $data) {
            $indexes = array_flip($data);
            if ($expectSuccess) {
                $this->assertCount($nplural, $indexes, "Langcode '$langCode' has '$nplural' plural forms.");
            } else {
                $this->assertNotCount($nplural, $indexes, "Langcode '$langCode' has '$nplural' plural forms.");
            }
        }
    }

    protected function generateTestData($langCodes)
    {
        $translator = new class {
            use TranslatorTrait {
                getPluralizationRule as public;
            }
        };

        $matrix = [];
        foreach ($langCodes as $langCode) {
            for ($count = 0; $count < 200; ++$count) {
                $plural = $translator->getPluralizationRule($count, $langCode);
                $matrix[$langCode][$count] = $plural;
            }
        }

        return $matrix;
    }
}
