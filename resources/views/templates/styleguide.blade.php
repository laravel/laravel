@extends('app/styleguide', [
	'model' => [
		'fonts' => [
			'font-body',
			'font-heading',
			'font-system',
		],
		'typography' => [
			[
				'name' => 'h1',
				'class' => 'e-h1',
				'copy' => 'The quick brown fox jumps over the lazy dog',
			],
			[
				'name' => 'h2',
				'class' => 'e-h2',
				'copy' => 'The quick brown fox jumps over the lazy dog',
			],
			[
				'name' => 'h3',
				'class' => 'e-h3',
				'copy' => 'The quick brown fox jumps over the lazy dog',
			],
			[
				'name' => 'h4',
				'class' => 'e-h4',
				'copy' => 'The quick brown fox jumps over the lazy dog',
			],
			[
				'name' => 'h5',
				'class' => 'e-h5',
				'copy' => 'The quick brown fox jumps over the lazy dog',
			],
			[
				'name' => 'h6',
				'class' => 'e-h6',
				'copy' => 'The quick brown fox jumps over the lazy dog',
			],
			[
				'name' => 'body',
				'class' => 'e-copy',
				'copy' => '
					<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>

					<ul>
						<li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium</li>

						<li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium</li>
					</ul>

					<ol>
						<li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium</li>

						<li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium</li>
					</ol>
				',
			],
		],
		'buttons' => [
			[
				'title' => 'Button',
				'url' => 'XXX',
			],
		],
		'colours' => [
 			'brand-red',
 			'black',
 			'white',
 			'grey-100',
			'grey-200',
			'grey-300',
			'grey-400',
			'grey-500',
			'grey-600',
			'grey-700',
			'grey-800',
			'grey-900',
 			'blue',
 			'green',
 			'red',
 			'social-twitter',
			'social-facebook',
			'social-youtube',
			'social-pinterest',
			'social-linkedin',
			'social-instagram',
		],
		'icons' => [
			'check',
			'chevron-down',
			'chevron-left',
			'chevron-right',
			'chevron-up',
			'logo',
		],
	],
])
