<?php

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
				'icon' => [
					'heading' => 'Icons',
					'copy' => 'Est commodo labore do eiusmod ea aute ea exercitation. Et quis ea aliqua culpa cupidatat pariatur sunt. Eu eiusmod dolor ut duis ad.',
					'previews' => [
						'logo' => [
							'heading' => 'Logo',
							'copy' => 'Ad do voluptate ipsum commodo nulla irure exercitation. Occaecat ullamco veniam velit non elit nisi deserunt ullamco. Eu sit excepteur et esse nulla voluptate laboris do.',
							'component' => [
								'name' => 'icon',
								'type' => 'vue',
							],
							'style' => 'font-size: 200px; line-height: 1',
							'attributes' => [
								[
									'name' => 'logo',
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
				'placeholder' => [
					'heading' => 'Placeholders',
					'previews' => [
						[
							'component' => [
								'name' => 'placeholder',
								'type' => 'blade',
							],
							'stack' => true,
							'style' => 'max-width: 200px',
							'attributes' => [
								[
									'modifier' => null,
									'src' => '//placehold.it/400x225',
									'title' => 'Alt text',
								],
								[
									'modifier' => 'landscape',
									'src' => '//placehold.it/400x300',
									'title' => 'Alt text',
								],
								[
									'modifier' => 'portrait',
									'src' => '//placehold.it/300x400',
									'title' => 'Alt text',
								],
								[
									'modifier' => 'square',
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
