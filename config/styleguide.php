<?php

// colours
$variables = file_get_contents(resource_path('assets/variables.json'));
$variablesArray = json_decode($variables, JSON_OBJECT_AS_ARRAY);
$keys = array_keys((array)$variablesArray['colors']);
$colorsArr = [];
if (isset($variablesArray['colors']) && is_array($variablesArray['colors'])) {
	foreach ($variablesArray['colors'] as $key => $colors) {
		array_push($colorsArr, $key);
	}
}

// icons
function icon($file) {
	return str_replace('.svg', '', basename($file));
}

$iconFiles = glob('../resources/assets/sprite/*.svg');
$icons = [];

foreach ($iconFiles as $icon) {
	array_push($icons, [
		'name' => str_replace('.svg', '', basename($icon)),
	]);
}

return [
	'meta' => [
		'title' => 'Styleguide',
		'icon' => '/static/img/meta/favicon-32.png',
		'accent' => '#ff585d',
	],
	'sections' => [
		'foundation' => [
			'heading' => 'Foundations',
			'copy' => 'Basic elements such as fonts, typography and colours.',
			'blocks' => [
				'font' => [
					'heading' => 'Fonts',
					'copy' => 'Culpa consequat incididunt ea id ad in labore nostrud. Sunt enim eu laboris laborum qui fugiat elit consectetur Lorem mollit adipisicing velit.',
					'previews' => [
						'sans-serif' => [
							'heading' => 'Sans-serif',
							'copy' => 'Anim magna sit amet elit irure nisi ex et. Elit qui aliquip labore cupidatat ullamco in enim esse aute.',
							'partial' => 'foundation/fonts',
							'style' => 'font-family: sans-serif',
							'attributes' => [
								[
									'weights' => [400, 700],
								],
							],
						],
						'serif' => [
							'heading' => 'Serif',
							'copy' => 'Exercitation reprehenderit quis reprehenderit nisi in dolor. Ipsum quis veniam ipsum laboris nisi.',
							'partial' => 'foundation/fonts',
							'style' => 'font-family: serif',
							'attributes' => [
								[
									'weights' => [400, 700],
								],
							],
						],
					],
				],
				'colour' => [
					'heading' => 'Colours',
					'previews' => [
						[
							'partial' => 'foundation/colours',
							'attributes' => [
								[
									'colours' => $colorsArr,
								]
							],
						],
					],
				],
				'icon' => [
					'heading' => 'Icons',
					'copy' => 'Est commodo labore do eiusmod ea aute ea exercitation. Et quis ea aliqua culpa cupidatat pariatur sunt. Eu eiusmod dolor ut duis ad.',
					'previews' => [
						[
							'component' => [
								'name' => 'icon',
								'type' => 'vue',
							],
							'style' => 'font-size: 50px; line-height: 1',
							'attributes' => $icons,
							'stack' => false,
						],
					],
				],
				'table' => [
					'heading' => 'Table',
					'copy' => 'Est commodo labore do eiusmod ea aute ea exercitation. Et quis ea aliqua culpa cupidatat pariatur sunt. Eu eiusmod dolor ut duis ad.',
					'previews' => [
						[
							'component' => [
								'name' => 'e-table',
								'type' => 'vue',
							],
							'stack' => true,
							'attributes' => [
								[
									'headers' => [
										'Caption X',
										'Caption Y',
									],
									'content' => [
										[
											'Content row x',
											'Content row y',
										],
										[
											'Content row x',
											'Content row y',
										],
										[
											'Content row x',
											'Content row y',
										],
									],
								],
							],
						],
					],
				],
			],
		],
		'block' => [
			'heading' => 'Blocks',
			'copy' => 'More complex components',
			'blocks' => [
				'breadcrumb' => [
					'heading' => 'Breadcrumb',
					'previews' => [
						[
							'component' => [
								'name' => 'breadcrumb',
								'type' => 'vue',
							],
							'autoload' => false,
							'attributes' => [
								[
									'pages' => [
										[
											'title' => 'Home',
											'url' => '#',
										],
										[
											'title' => 'Parent page',
											'url' => '#',
										],
										[
											'title' => 'Current page',
											'url' => '#',
										],
									],
								],
							],
						],
					],
				],
				'pagination' => [
					'heading' => 'Pagination',
					'previews' => [
						[
							'component' => [
								'name' => 'pagination',
								'type' => 'vue',
							],
							'autoload' => false,
							'container' => true,
							'attributes' => [
								[
									'pages' => [
										[
											'title' => 'First',
											'type' => 'jump',
											'disabled' => true,
										],
										[
											'title' => 'Prev',
											'type' => 'prev',
											'disabled' => true,
										],
										[
											'title' => '1',
											'url' => '#',
											'current' => true,
										],
										[
											'title' => '2',
											'url' => '#',
										],
										[
											'title' => '3',
											'url' => '#',
										],
										[
											'title' => '&hellip;',
											'type' => 'gap',
										],
										[
											'title' => '40',
											'url' => '#',
											'type' => 'page-end'
										],
										[
											'title' => '41',
											'url' => '#',
											'type' => 'page-end'
										],
										[
											'title' => '42',
											'url' => '#',
											'type' => 'page-end'
										],
										[
											'title' => 'Next',
											'url' => '#',
											'type' => 'next',
										],
										[
											'title' => 'Last',
											'url' => '#',
											'type' => 'jump',
										],
									],
								],
							],
						],
					],
				],
				'placeholder' => [
					'heading' => 'Placeholders',
					'previews' => [
						[
							'component' => [
								'name' => 'placeholder',
								'type' => 'blade',
							],
							'autoload' => false,
							'stack' => true,
							'style' => 'width: 200px',
							'attributes' => [
								[
									'modifier' => null,
									'src' => '//placehold.it/400x225',
									'title' => 'Alt text',
								],
								[
									'modifier' => '3/4',
									'src' => '//placehold.it/400x300',
									'title' => 'Alt text',
								],
								[
									'modifier' => '4/3',
									'src' => '//placehold.it/300x400',
									'title' => 'Alt text',
								],
								[
									'modifier' => 'full',
									'src' => '//placehold.it/400',
									'title' => 'Alt text',
								],
							],
						],
					],
				],
			],
		],
	],
];
