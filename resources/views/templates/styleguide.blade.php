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
					<h2>Sed ut perspiciatis unde omnis iste natus error</h2>

					<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>

					<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>

					<h3>Sed ut perspiciatis unde omnis iste natus error</h3>

					<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>

					<h4>Sed ut perspiciatis unde omnis iste natus error</h4>

					<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>

					<h2>Sed ut perspiciatis unde omnis iste natus error</h2>

					<h3>Sed ut perspiciatis unde omnis iste natus error</h3>

					<h4>Sed ut perspiciatis unde omnis iste natus error</h4>

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
				'bg' => 'bg-grey-100',
				'items' => [
					[
						'title' => 'Visit our website',
						'href' => '#',
					],
				],
			],
		],
		'colours' => [
			'black',
			'white',
			'grey-50',
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
			'chevron-right',
		],
		'images' => [
			[
				'src' => [
					'http://via.placeholder.com/2000x2400/888/000',
					'http://via.placeholder.com/1600x900/888/000',
					'http://via.placeholder.com/500x200/888/000',
				],
				'webp' => [
					'http://via.placeholder.com/2000x2400.webp/888/000',
					'http://via.placeholder.com/1600x900.webp/888/000',
					'http://via.placeholder.com/500x200.webp/888/000',
				],
				'sizes' => [
					1248,
					768,
				],
			],
		],
		'forms' => [
			[
				'action' => route('templates.echo', [
					'json' => [
						'redirect' => null,
						'response' => [
							'title' => 'Thanks for your submission',
						],
					],
					// Simulated form errors
					// 'status' => 422,
					// 'json' => [
					// 	'errors' => [
					// 		'email' => [
					// 			ucfirst(_mock()->wordsBetween(5, 8)),
					// 		],
					// 	],
					// 	'message' => ucfirst(_mock()->wordsBetween(5, 8)),
					// ],
				]),
				'values' => [
					'first_name' => 'Rick',
					'last_name' => 'Astley',
					'email' => 'rick.astley@email.com',
					'terms' => true,
					'activity' => 'climbing',
					'transport' => 'bus',
				],
				'schema' => [
					[
						'as' => 'input',
						'label' => 'First name',
						'name' => 'first_name',
						'placeholder' => 'Your first name',
						'rules' => 'required',
					],
					[
						'as' => 'input',
						'label' => 'Last name',
						'name' => 'last_name',
						'placeholder' => 'Your last name',
						'rules' => 'required',
					],
					[
						'as' => 'input',
						'type' => 'email',
						'label' => 'Email Address',
						'name' => 'email',
						'placeholder' => 'Your email address',
						'rules' => 'required|email',
					],
					[
						'as' => 'input',
						'type' => 'tel',
						'label' => 'Phone number',
						'name' => 'phone',
						'placeholder' => 'Your phone number',
					],
					[
						'as' => 'textarea',
						'label' => 'Your comments',
						'name' => 'comments',
						'placeholder' => 'Your comments',
					],
					[
						'as' => 'checkbox',
						'label' => 'Terms and conditions Aperiam necessitatibus culpa iusto dolor accusantium Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perspiciatis voluptatem suscipit vitae, vel ullam facere accusantium distinctio quibusdam nam architecto voluptates aut, libero officiis necessitatibus quaerat hic quia culpa unde?',
						'name' => 'terms',
						'validation-name' => 'Terms and Conditions',
						'rules' => 'required',
					],
					[
						'as' => 'radio',
						'label' => 'Activity',
						'name' => 'activity',
						'options' => [
							[
								'value' => 'walking',
								'label' => 'Walking',
							],
							[
								'value' => 'running',
								'label' => 'Running',
							],
							[
								'value' => 'climbing',
								'label' => 'Climbing',
							],
						],
						'rules' => 'required',
					],
					[
						'as' => 'select',
						'label' => 'Transport',
						'name' => 'transport',
						'placeholder' => 'Transportation type',
						'options' => [
							[
								'value' => 'car',
								'label' => 'Car',
							],
							[
								'value' => 'train',
								'label' => 'Train',
							],
							[
								'value' => 'bus',
								'label' => 'Bus',
							],
						],
						'rules' => 'required',
					],
					[
						'as' => 'submit',
						'title' => 'Submit',
					],
				],
			],
		],
	],
])
