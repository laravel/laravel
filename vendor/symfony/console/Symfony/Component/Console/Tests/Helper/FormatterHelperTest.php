<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Helper;

use Symfony\Component\Console\Helper\FormatterHelper;

class FormatterHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatSection()
    {
        $formatter = new FormatterHelper();

        $this->assertEquals(
            '<info>[cli]</info> Some text to display',
            $formatter->formatSection('cli', 'Some text to display'),
            '::formatSection() formats a message in a section'
        );
    }

    public function testFormatBlock()
    {
        $formatter = new FormatterHelper();

        $this->assertEquals(
            '<error> Some text to display </error>',
            $formatter->formatBlock('Some text to display', 'error'),
            '::formatBlock() formats a message in a block'
        );

        $this->assertEquals(
            '<error> Some text to display </error>'."\n".
            '<error> foo bar              </error>',
            $formatter->formatBlock(array('Some text to display', 'foo bar'), 'error'),
            '::formatBlock() formats a message in a block'
        );

        $this->assertEquals(
            '<error>                        </error>'."\n".
            '<error>  Some text to display  </error>'."\n".
            '<error>                        </error>',
            $formatter->formatBlock('Some text to display', 'error', true),
            '::formatBlock() formats a message in a block'
        );
    }

    public function testFormatBlockWithDiacriticLetters()
    {
        if (!function_exists('mb_detect_encoding')) {
            $this->markTestSkipped('This test requires mbstring to work.');
        }

        $formatter = new FormatterHelper();

        $this->assertEquals(
            '<error>                       </error>'."\n".
            '<error>  Du texte à afficher  </error>'."\n".
            '<error>                       </error>',
            $formatter->formatBlock('Du texte à afficher', 'error', true),
            '::formatBlock() formats a message in a block'
        );
    }

    public function testFormatBlockWithDoubleWidthDiacriticLetters()
    {
        if (!extension_loaded('mbstring')) {
            $this->markTestSkipped('This test requires mbstring to work.');
        }
        $formatter = new FormatterHelper();
        $this->assertEquals(
            '<error>                    </error>'."\n".
            '<error>  表示するテキスト  </error>'."\n".
            '<error>                    </error>',
            $formatter->formatBlock('表示するテキスト', 'error', true),
            '::formatBlock() formats a message in a block'
        );
    }

    public function testFormatBlockLGEscaping()
    {
        $formatter = new FormatterHelper();

        $this->assertEquals(
            '<error>                            </error>'."\n".
            '<error>  \<info>some info\</info>  </error>'."\n".
            '<error>                            </error>',
            $formatter->formatBlock('<info>some info</info>', 'error', true),
            '::formatBlock() escapes \'<\' chars'
        );
    }
}
