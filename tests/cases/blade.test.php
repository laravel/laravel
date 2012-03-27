<?php

use Laravel\Blade;

class BladeTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the compilation of echo statements.
	 *
	 * @group laravel
	 */
	public function testEchosAreConvertedProperly()
	{
		$blade1 = '{{$a}}';
		$blade2 = '{{e($a)}}';

		$this->assertEquals('<?php echo $a; ?>', Blade::compile_string($blade1));
		$this->assertEquals('<?php echo e($a); ?>', Blade::compile_string($blade2));
	}

	/**
	 * Test the compilation of control structures.
	 *
	 * @group laravel
	 */
	public function testControlStructuresAreCreatedCorrectly()
	{
		$blade1 = "@if (true)\nfoo\n@endif";
		$blade2 = "@if (count(".'$something'.") > 0)\nfoo\n@endif";
		$blade3 = "@if (true)\nfoo\n@elseif (false)\nbar\n@endif";
		$blade4 = "@if (true)\nfoo\n@else\nbar\n@endif";

		$this->assertEquals("<?php if (true): ?>\nfoo\n<?php endif; ?>", Blade::compile_string($blade1));
		$this->assertEquals("<?php if (count(".'$something'.") > 0): ?>\nfoo\n<?php endif; ?>", Blade::compile_string($blade2));
		$this->assertEquals("<?php if (true): ?>\nfoo\n<?php elseif (false): ?>\nbar\n<?php endif; ?>", Blade::compile_string($blade3));
		$this->assertEquals("<?php if (true): ?>\nfoo\n<?php else: ?>\nbar\n<?php endif; ?>", Blade::compile_string($blade4));
	}

	/**
	 * Test the compilation of yield statements.
	 *
	 * @group laravel
	 */
	public function testYieldsAreCompiledCorrectly()
	{
		$blade = "@yield('something')";

		$this->assertEquals("<?php echo \\Laravel\\Section::yield('something'); ?>", Blade::compile_string($blade));
	}

	/**
	 * Test the compilation of section statements.
	 *
	 * @group laravel
	 */
	public function testSectionsAreCompiledCorrectly()
	{
		$blade = "@section('something')\nfoo\n@endsection";

		$this->assertEquals("<?php \\Laravel\\Section::start('something'); ?>\nfoo\n<?php \\Laravel\\Section::stop(); ?>", Blade::compile_string($blade));
	}

}