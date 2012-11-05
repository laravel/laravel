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
	 * Test the compilation of comments statements.
	 *
	 * @group laravel
	 */
	public function testCommentsAreConvertedProperly()
	{
		$blade1 = "{{-- This is a comment --}}";
		$blade2 = "{{--\nThis is a\nmulti-line\ncomment.\n--}}";

		$this->assertEquals("<?php /*  This is a comment  */ ?>\n", Blade::compile_string($blade1));
		$this->assertEquals("<?php /* \nThis is a\nmulti-line\ncomment.\n */ ?>\n", Blade::compile_string($blade2));
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
		$blade3 = "@if (true)\nfoo\n@elseif (false)\nbar\n@else\nfoobar\n@endif";
		$blade4 = "@if (true)\nfoo\n@elseif (false)\nbar\n@endif";
		$blade5 = "@if (true)\nfoo\n@else\nbar\n@endif";
		$blade6 = "@unless (count(".'$something'.") > 0)\nfoobar\n@endunless";
		$blade7 = "@for (Foo::all() as ".'$foo'.")\nfoo\n@endfor";
		$blade8 = "@foreach (Foo::all() as ".'$foo'.")\nfoo\n@endforeach";
		$blade9 = "@forelse (Foo::all() as ".'$foo'.")\nfoo\n@empty\nbar\n@endforelse";
		$blade10 = "@while (true)\nfoo\n@endwhile";
		$blade11 = "@while (Foo::bar())\nfoo\n@endwhile";


		$this->assertEquals("<?php if (true): ?>\nfoo\n<?php endif; ?>", Blade::compile_string($blade1));
		$this->assertEquals("<?php if (count(".'$something'.") > 0): ?>\nfoo\n<?php endif; ?>", Blade::compile_string($blade2));
		$this->assertEquals("<?php if (true): ?>\nfoo\n<?php elseif (false): ?>\nbar\n<?php else: ?>\nfoobar\n<?php endif; ?>", Blade::compile_string($blade3));
		$this->assertEquals("<?php if (true): ?>\nfoo\n<?php elseif (false): ?>\nbar\n<?php endif; ?>", Blade::compile_string($blade4));
		$this->assertEquals("<?php if (true): ?>\nfoo\n<?php else: ?>\nbar\n<?php endif; ?>", Blade::compile_string($blade5));
		$this->assertEquals("<?php if ( ! ( (count(".'$something'.") > 0))): ?>\nfoobar\n<?php endif; ?>", Blade::compile_string($blade6));
		$this->assertEquals("<?php for (Foo::all() as ".'$foo'."): ?>\nfoo\n<?php endfor; ?>", Blade::compile_string($blade7));
		$this->assertEquals("<?php foreach (Foo::all() as ".'$foo'."): ?>\nfoo\n<?php endforeach; ?>", Blade::compile_string($blade8));
		$this->assertEquals("<?php if (count(Foo::all()) > 0): ?><?php foreach (Foo::all() as ".'$foo'."): ?>\nfoo\n<?php endforeach; ?><?php else: ?>\nbar\n<?php endif; ?>", Blade::compile_string($blade9));
		$this->assertEquals("<?php while (true): ?>\nfoo\n<?php endwhile; ?>", Blade::compile_string($blade10));
		$this->assertEquals("<?php while (Foo::bar()): ?>\nfoo\n<?php endwhile; ?>", Blade::compile_string($blade11));
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

	/**
	 * Test the compilation of include statements.
	 *
	 * @group laravel
	 */
	public function testIncludesAreCompiledCorrectly()
	{
		$blade1 = "@include('user.profile')";
		$blade2 = "@include(Config::get('application.default_view', 'user.profile'))";

		$this->assertEquals("<?php echo view('user.profile')->with(get_defined_vars())->render(); ?>", Blade::compile_string($blade1));
		$this->assertEquals("<?php echo view(Config::get('application.default_view', 'user.profile'))->with(get_defined_vars())->render(); ?>", Blade::compile_string($blade2));
	}

	/**
	 * Test the compilation of render statements.
	 *
	 * @group laravel
	 */
	public function testRendersAreCompiledCorrectly()
	{
		$blade1 = "@render('user.profile')";
		$blade2 = "@render(Config::get('application.default_view', 'user.profile'))";

		$this->assertEquals("<?php echo render('user.profile'); ?>", Blade::compile_string($blade1));
		$this->assertEquals("<?php echo render(Config::get('application.default_view', 'user.profile')); ?>", Blade::compile_string($blade2));

	}

}