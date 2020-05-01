@extends('app/contact/view', [
	'model' => [
		'action' => route('frontend.echo', [
			'status' => 422,
			'json' => [
				'errors' => [
					'firstName' => [
						'Please enter your first name',
					],
					'message' => [
						'Please accept the privacy policy',
					],
					'privacy' => [
						'Please accept the privacy policy',
					],
				],
			],
		]),
		'fields' => [
			[
				'name' => 'firstName',
				'label' => trans('global.form.labels.firstName'),
			],
			[
				'name' => 'lastName',
				'label' => trans('global.form.labels.lastName'),
			],
			[
				'name' => 'email',
				'label' => trans('global.form.labels.email'),
				'type' => 'email',
				'inputmode' => 'email',
			],
			[
				'name' => 'telephone',
				'label' => trans('global.form.labels.telephone'),
				'type' => 'tel',
				'inputmode' => 'tel',
			],
			[
				'name' => 'message',
				'label' => trans('global.form.labels.message'),
				'element' => 'textarea',
				'rows' => '6',
			],
			[
				'name' => 'subject',
				'label' => trans('global.form.labels.subject'),
				'element' => 'e-select',
				'options' => [
					1 => 'Reason 1',
					2 => 'Reason 2',
					3 => 'Reason 3',
				],
				// 'options' => [
				// 	'Reason 1',
				// 	'Reason 2',
				// 	'Reason 3',
				// ],
			],
			[
				'component' => 'checkbox-wrapper',
				'name' => 'newsletter',
				'label' => trans('global.form.labels.newsletter'),
				// 'trueValue' => 'true value',
			],
			[
				'component' => 'checkbox-wrapper',
				'name' => 'privacy',
				'label' => trans('global.form.labels.privacy', [
					'privacyLink' => sprintf('<a href="#">%s</a>', trans('global.form.labels.privacyLink')),
				]),
			],
			// [
			// 	'component' => 'checkbox-wrapper',
			// 	'name' => 'option',
			// 	'label' => 'option',
			// 	'type' => 'radio',
			// 	'trueValue' => 'Radio value 1',
			// ],
			// [
			// 	'component' => 'checkbox-wrapper',
			// 	'name' => 'option',
			// 	'label' => 'option',
			// 	'type' => 'radio',
			// 	'trueValue' => 'Radio value 2',
			// ],
			// [
			// 	'component' => 'checkbox-wrapper',
			// 	'name' => 'option',
			// 	'label' => 'option',
			// 	'type' => 'radio',
			// 	'trueValue' => 'Radio value 3',
			// 	'disabled' => true,
			// ],
			// [
			// 	'component' => 'checkbox-wrapper',
			// 	'name' => 'option',
			// 	'label' => 'option',
			// 	'type' => 'radio',
			// 	'trueValue' => 'Radio value 4',
			// ],
			[
				'component' => 'e-button',
				'text' => trans('global.form.cta.submit'),
			],
		],
	],
])
