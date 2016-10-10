<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Adapter;

class FinderTest extends Iterator\RealIteratorTestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf('Symfony\Component\Finder\Finder', Finder::create());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testDirectories($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->directories());
        $this->assertIterator($this->toAbsolute(array('foo', 'toto')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->directories();
        $finder->files();
        $finder->directories();
        $this->assertIterator($this->toAbsolute(array('foo', 'toto')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testFiles($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->files());
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'test.py', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->files();
        $finder->directories();
        $finder->files();
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'test.py', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testDepth($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->depth('< 1'));
        $this->assertIterator($this->toAbsolute(array('foo', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->depth('<= 0'));
        $this->assertIterator($this->toAbsolute(array('foo', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->depth('>= 1'));
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->depth('< 1')->depth('>= 1');
        $this->assertIterator(array(), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testName($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->name('*.php'));
        $this->assertIterator($this->toAbsolute(array('test.php')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->name('test.ph*');
        $finder->name('test.py');
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->name('~^test~i');
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->name('~\\.php$~i');
        $this->assertIterator($this->toAbsolute(array('test.php')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->name('test.p{hp,y}');
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testNotName($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->notName('*.php'));
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->notName('*.php');
        $finder->notName('*.py');
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->name('test.ph*');
        $finder->name('test.py');
        $finder->notName('*.php');
        $finder->notName('*.py');
        $this->assertIterator(array(), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->name('test.ph*');
        $finder->name('test.py');
        $finder->notName('*.p{hp,y}');
        $this->assertIterator(array(), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getRegexNameTestData
     *
     * @group regexName
     */
    public function testRegexName($adapter, $regex)
    {
        $finder = $this->buildFinder($adapter);
        $finder->name($regex);
        $this->assertIterator($this->toAbsolute(array('test.py', 'test.php')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSize($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->files()->size('< 1K')->size('> 500'));
        $this->assertIterator($this->toAbsolute(array('test.php')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testDate($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->files()->date('until last month'));
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testExclude($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->exclude('foo'));
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testIgnoreVCS($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->ignoreVCS(false)->ignoreDotFiles(false));
        $this->assertIterator($this->toAbsolute(array('.git', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->ignoreVCS(false)->ignoreVCS(false)->ignoreDotFiles(false);
        $this->assertIterator($this->toAbsolute(array('.git', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->ignoreVCS(true)->ignoreDotFiles(false));
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testIgnoreDotFiles($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->ignoreDotFiles(false)->ignoreVCS(false));
        $this->assertIterator($this->toAbsolute(array('.git', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $finder->ignoreDotFiles(false)->ignoreDotFiles(false)->ignoreVCS(false);
        $this->assertIterator($this->toAbsolute(array('.git', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->ignoreDotFiles(true)->ignoreVCS(false));
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSortByName($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->sortByName());
        $this->assertIterator($this->toAbsolute(array('foo', 'foo bar', 'foo/bar.tmp', 'test.php', 'test.py', 'toto')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSortByType($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->sortByType());
        $this->assertIterator($this->toAbsolute(array('foo', 'foo bar', 'toto', 'foo/bar.tmp', 'test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSortByAccessedTime($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->sortByAccessedTime());
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'toto', 'test.py', 'foo', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSortByChangedTime($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->sortByChangedTime());
        $this->assertIterator($this->toAbsolute(array('toto', 'test.py', 'test.php', 'foo/bar.tmp', 'foo', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSortByModifiedTime($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->sortByModifiedTime());
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'toto', 'test.py', 'foo', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSort($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->sort(function (\SplFileInfo $a, \SplFileInfo $b) { return strcmp($a->getRealpath(), $b->getRealpath()); }));
        $this->assertIterator($this->toAbsolute(array('foo', 'foo bar', 'foo/bar.tmp', 'test.php', 'test.py', 'toto')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testFilter($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->filter(function (\SplFileInfo $f) { return preg_match('/test/', $f) > 0; }));
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testFollowLinks($adapter)
    {
        if ('\\' == DIRECTORY_SEPARATOR) {
            return;
        }

        $finder = $this->buildFinder($adapter);
        $this->assertSame($finder, $finder->followLinks());
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testIn($adapter)
    {
        $finder = $this->buildFinder($adapter);
        try {
            $finder->in('foobar');
            $this->fail('->in() throws a \InvalidArgumentException if the directory does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e, '->in() throws a \InvalidArgumentException if the directory does not exist');
        }

        $finder = $this->buildFinder($adapter);
        $iterator = $finder->files()->name('*.php')->depth('< 1')->in(array(self::$tmpDir, __DIR__))->getIterator();

        $this->assertIterator(array(self::$tmpDir.DIRECTORY_SEPARATOR.'test.php', __DIR__.DIRECTORY_SEPARATOR.'FinderTest.php'), $iterator);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testInWithGlob($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $finder->in(array(__DIR__.'/Fixtures/*/B/C', __DIR__.'/Fixtures/*/*/B/C'))->getIterator();

        $this->assertIterator($this->toAbsoluteFixtures(array('A/B/C/abc.dat', 'copy/A/B/C/abc.dat.copy')), $finder);
    }

    /**
     * @dataProvider getAdaptersTestData
     * @expectedException \InvalidArgumentException
     */
    public function testInWithNonDirectoryGlob($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $finder->in(__DIR__.'/Fixtures/A/a*');
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetIterator($adapter)
    {
        $finder = $this->buildFinder($adapter);
        try {
            $finder->getIterator();
            $this->fail('->getIterator() throws a \LogicException if the in() method has not been called');
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e, '->getIterator() throws a \LogicException if the in() method has not been called');
        }

        $finder = $this->buildFinder($adapter);
        $dirs = array();
        foreach ($finder->directories()->in(self::$tmpDir) as $dir) {
            $dirs[] = (string) $dir;
        }

        $expected = $this->toAbsolute(array('foo', 'toto'));

        sort($dirs);
        sort($expected);

        $this->assertEquals($expected, $dirs, 'implements the \IteratorAggregate interface');

        $finder = $this->buildFinder($adapter);
        $this->assertEquals(2, iterator_count($finder->directories()->in(self::$tmpDir)), 'implements the \IteratorAggregate interface');

        $finder = $this->buildFinder($adapter);
        $a = iterator_to_array($finder->directories()->in(self::$tmpDir));
        $a = array_values(array_map(function ($a) { return (string) $a; }, $a));
        sort($a);
        $this->assertEquals($expected, $a, 'implements the \IteratorAggregate interface');
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testRelativePath($adapter)
    {
        $finder = $this->buildFinder($adapter)->in(self::$tmpDir);

        $paths = array();

        foreach ($finder as $file) {
            $paths[] = $file->getRelativePath();
        }

        $ref = array("", "", "", "", "foo", "");

        sort($ref);
        sort($paths);

        $this->assertEquals($ref, $paths);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testRelativePathname($adapter)
    {
        $finder = $this->buildFinder($adapter)->in(self::$tmpDir)->sortByName();

        $paths = array();

        foreach ($finder as $file) {
            $paths[] = $file->getRelativePathname();
        }

        $ref = array("test.php", "toto", "test.py", "foo", "foo".DIRECTORY_SEPARATOR."bar.tmp", "foo bar");

        sort($paths);
        sort($ref);

        $this->assertEquals($ref, $paths);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testAppendWithAFinder($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $finder->files()->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder1 = $this->buildFinder($adapter);
        $finder1->directories()->in(self::$tmpDir);

        $finder = $finder->append($finder1);

        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'toto')), $finder->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testAppendWithAnArray($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $finder->files()->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder->append($this->toAbsolute(array('foo', 'toto')));

        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'toto')), $finder->getIterator());
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testAppendReturnsAFinder($adapter)
    {
        $this->assertInstanceOf('Symfony\\Component\\Finder\\Finder', $this->buildFinder($adapter)->append(array()));
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testAppendDoesNotRequireIn($adapter)
    {
        $finder = $this->buildFinder($adapter);
        $finder->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder1 = Finder::create()->append($finder);

        $this->assertIterator(iterator_to_array($finder->getIterator()), $finder1->getIterator());
    }

    public function testCountDirectories()
    {
        $directory = Finder::create()->directories()->in(self::$tmpDir);
        $i = 0;

        foreach ($directory as $dir) {
            $i++;
        }

        $this->assertCount($i, $directory);
    }

    public function testCountFiles()
    {
        $files = Finder::create()->files()->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures');
        $i = 0;

        foreach ($files as $file) {
            $i++;
        }

        $this->assertCount($i, $files);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCountWithoutIn()
    {
        $finder = Finder::create()->files();
        count($finder);
    }

    /**
     * @dataProvider getContainsTestData
     * @group grep
     */
    public function testContains($adapter, $matchPatterns, $noMatchPatterns, $expected)
    {
        $finder = $this->buildFinder($adapter);
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures')
            ->name('*.txt')->sortByName()
            ->contains($matchPatterns)
            ->notContains($noMatchPatterns);

        $this->assertIterator($this->toAbsoluteFixtures($expected), $finder);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testContainsOnDirectory(Adapter\AdapterInterface $adapter)
    {
        $finder = $this->buildFinder($adapter);
        $finder->in(__DIR__)
            ->directories()
            ->name('Fixtures')
            ->contains('abc');
        $this->assertIterator(array(), $finder);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testNotContainsOnDirectory(Adapter\AdapterInterface $adapter)
    {
        $finder = $this->buildFinder($adapter);
        $finder->in(__DIR__)
            ->directories()
            ->name('Fixtures')
            ->notContains('abc');
        $this->assertIterator(array(), $finder);
    }

    /**
     * Searching in multiple locations involves AppendIterator which does an unnecessary rewind which leaves FilterIterator
     * with inner FilesystemIterator in an invalid state.
     *
     * @see https://bugs.php.net/bug.php?id=49104
     *
     * @dataProvider getAdaptersTestData
     */
    public function testMultipleLocations(Adapter\AdapterInterface $adapter)
    {
        $locations = array(
            self::$tmpDir.'/',
            self::$tmpDir.'/toto/',
        );

        // it is expected that there are test.py test.php in the tmpDir
        $finder = $this->buildFinder($adapter);
        $finder->in($locations)->depth('< 1')->name('test.php');

        $this->assertCount(1, $finder);
    }

    /**
     * Iterator keys must be the file pathname.
     *
     * @dataProvider getAdaptersTestData
     */
    public function testIteratorKeys(Adapter\AdapterInterface $adapter)
    {
        $finder = $this->buildFinder($adapter)->in(self::$tmpDir);
        foreach ($finder as $key => $file) {
            $this->assertEquals($file->getPathname(), $key);
        }
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testRegexSpecialCharsLocationWithPathRestrictionContainingStartFlag(Adapter\AdapterInterface $adapter)
    {
        $finder = $this->buildFinder($adapter);
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'r+e.gex[c]a(r)s')
            ->path('/^dir/');

        $expected = array('r+e.gex[c]a(r)s'.DIRECTORY_SEPARATOR.'dir',
                          'r+e.gex[c]a(r)s'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'bar.dat',);
        $this->assertIterator($this->toAbsoluteFixtures($expected), $finder);
    }

    public function testAdaptersOrdering()
    {
        $finder = Finder::create()
            ->removeAdapters()
            ->addAdapter(new FakeAdapter\NamedAdapter('a'), 0)
            ->addAdapter(new FakeAdapter\NamedAdapter('b'), -50)
            ->addAdapter(new FakeAdapter\NamedAdapter('c'), 50)
            ->addAdapter(new FakeAdapter\NamedAdapter('d'), -25)
            ->addAdapter(new FakeAdapter\NamedAdapter('e'), 25);

        $this->assertEquals(
            array('c', 'e', 'a', 'd', 'b'),
            array_map(function (Adapter\AdapterInterface $adapter) {
                return $adapter->getName();
            }, $finder->getAdapters())
        );
    }

    public function testAdaptersChaining()
    {
        $iterator  = new \ArrayIterator(array());
        $filenames = $this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto'));
        foreach ($filenames as $file) {
            $iterator->append(new \Symfony\Component\Finder\SplFileInfo($file, null, null));
        }

        $finder = Finder::create()
            ->removeAdapters()
            ->addAdapter(new FakeAdapter\UnsupportedAdapter(), 3)
            ->addAdapter(new FakeAdapter\FailingAdapter(), 2)
            ->addAdapter(new FakeAdapter\DummyAdapter($iterator), 1);

        $this->assertIterator($filenames, $finder->in(sys_get_temp_dir())->getIterator());
    }

    public function getAdaptersTestData()
    {
        return array_map(
            function ($adapter) { return array($adapter); },
            $this->getValidAdapters()
        );
    }

    public function getContainsTestData()
    {
        $tests = array(
            array('', '', array()),
            array('foo', 'bar', array()),
            array('', 'foobar', array('dolor.txt', 'ipsum.txt', 'lorem.txt')),
            array('lorem ipsum dolor sit amet', 'foobar', array('lorem.txt')),
            array('sit', 'bar', array('dolor.txt', 'ipsum.txt', 'lorem.txt')),
            array('dolor sit amet', '@^L@m', array('dolor.txt', 'ipsum.txt')),
            array('/^lorem ipsum dolor sit amet$/m', 'foobar', array('lorem.txt')),
            array('lorem', 'foobar', array('lorem.txt')),
            array('', 'lorem', array('dolor.txt', 'ipsum.txt')),
            array('ipsum dolor sit amet', '/^IPSUM/m', array('lorem.txt')),
        );

        return $this->buildTestData($tests);
    }

    public function getRegexNameTestData()
    {
        $tests = array(
            array('~.+\\.p.+~i'),
            array('~t.*s~i'),
        );

        return $this->buildTestData($tests);
    }

    /**
     * @dataProvider getTestPathData
     */
    public function testPath(Adapter\AdapterInterface $adapter, $matchPatterns, $noMatchPatterns, array $expected)
    {
        $finder = $this->buildFinder($adapter);
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures')
            ->path($matchPatterns)
            ->notPath($noMatchPatterns);

        $this->assertIterator($this->toAbsoluteFixtures($expected), $finder);
    }

    public function testAdapterSelection()
    {
        // test that by default, PhpAdapter is selected
        $adapters = Finder::create()->getAdapters();
        $this->assertTrue($adapters[0] instanceof Adapter\PhpAdapter);

        // test another adapter selection
        $adapters = Finder::create()->setAdapter('gnu_find')->getAdapters();
        $this->assertTrue($adapters[0] instanceof Adapter\GnuFindAdapter);

        // test that useBestAdapter method removes selection
        $adapters = Finder::create()->useBestAdapter()->getAdapters();
        $this->assertFalse($adapters[0] instanceof Adapter\PhpAdapter);
    }

    public function getTestPathData()
    {
        $tests = array(
            array('', '', array()),
            array('/^A\/B\/C/', '/C$/',
                array('A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat'),
            ),
            array('/^A\/B/', 'foobar',
                array(
                    'A'.DIRECTORY_SEPARATOR.'B',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                ),
            ),
            array('A/B/C', 'foobar',
                array(
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat.copy',
                ),
            ),
            array('A/B', 'foobar',
                array(
                    //dirs
                    'A'.DIRECTORY_SEPARATOR.'B',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    //files
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat.copy',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat.copy',
                ),
            ),
            array('/^with space\//', 'foobar',
                array(
                    'with space'.DIRECTORY_SEPARATOR.'foo.txt',
                ),
            ),
        );

        return $this->buildTestData($tests);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testAccessDeniedException(Adapter\AdapterInterface $adapter)
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('chmod is not supported on Windows');
        }

        $finder = $this->buildFinder($adapter);
        $finder->files()->in(self::$tmpDir);

        // make 'foo' directory non-readable
        $testDir = self::$tmpDir.DIRECTORY_SEPARATOR.'foo';
        chmod($testDir, 0333);

        if (false === $couldRead = is_readable($testDir)) {
            try {
                $this->assertIterator($this->toAbsolute(array('foo bar', 'test.php', 'test.py')), $finder->getIterator());
                $this->fail('Finder should throw an exception when opening a non-readable directory.');
            } catch (\Exception $e) {
                $expectedExceptionClass = 'Symfony\\Component\\Finder\\Exception\\AccessDeniedException';
                if ($e instanceof \PHPUnit_Framework_ExpectationFailedException) {
                    $this->fail(sprintf("Expected exception:\n%s\nGot:\n%s\nWith comparison failure:\n%s", $expectedExceptionClass, 'PHPUnit_Framework_ExpectationFailedException', $e->getComparisonFailure()->getExpectedAsString()));
                }

                $this->assertInstanceOf($expectedExceptionClass, $e);
            }
        }

        // restore original permissions
        chmod($testDir, 0777);
        clearstatcache($testDir);

        if ($couldRead) {
            $this->markTestSkipped('could read test files while test requires unreadable');
        }
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testIgnoredAccessDeniedException(Adapter\AdapterInterface $adapter)
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('chmod is not supported on Windows');
        }

        $finder = $this->buildFinder($adapter);
        $finder->files()->ignoreUnreadableDirs()->in(self::$tmpDir);

        // make 'foo' directory non-readable
        $testDir = self::$tmpDir.DIRECTORY_SEPARATOR.'foo';
        chmod($testDir, 0333);

        if (false === ($couldRead = is_readable($testDir))) {
            $this->assertIterator($this->toAbsolute(array('foo bar', 'test.php', 'test.py')), $finder->getIterator());
        }

        // restore original permissions
        chmod($testDir, 0777);
        clearstatcache($testDir);

        if ($couldRead) {
            $this->markTestSkipped('could read test files while test requires unreadable');
        }
    }

    private function buildTestData(array $tests)
    {
        $data = array();
        foreach ($this->getValidAdapters() as $adapter) {
            foreach ($tests as $test) {
                $data[] = array_merge(array($adapter), $test);
            }
        }

        return $data;
    }

    private function buildFinder(Adapter\AdapterInterface $adapter)
    {
        return Finder::create()
            ->removeAdapters()
            ->addAdapter($adapter);
    }

    private function getValidAdapters()
    {
        return array_filter(
            array(
                new Adapter\BsdFindAdapter(),
                new Adapter\GnuFindAdapter(),
                new Adapter\PhpAdapter(),
            ),
            function (Adapter\AdapterInterface $adapter) {
                return $adapter->isSupported();
            }
        );
    }

    /**
     * Searching in multiple locations with sub directories involves
     * AppendIterator which does an unnecessary rewind which leaves
     * FilterIterator with inner FilesystemIterator in an invalid state.
     *
     * @see https://bugs.php.net/bug.php?id=49104
     */
    public function testMultipleLocationsWithSubDirectories()
    {
        $locations = array(
            __DIR__.'/Fixtures/one',
            self::$tmpDir.DIRECTORY_SEPARATOR.'toto',
        );

        $finder = new Finder();
        $finder->in($locations)->depth('< 10')->name('*.neon');

        $expected = array(
            __DIR__.'/Fixtures/one'.DIRECTORY_SEPARATOR.'b'.DIRECTORY_SEPARATOR.'c.neon',
            __DIR__.'/Fixtures/one'.DIRECTORY_SEPARATOR.'b'.DIRECTORY_SEPARATOR.'d.neon',
        );

        $this->assertIterator($expected, $finder);
        $this->assertIteratorInForeach($expected, $finder);
    }
}
