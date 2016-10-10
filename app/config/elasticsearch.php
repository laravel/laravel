<?php 
return [
	'hosts'=>[
		'http://search.poem.com:9200'
		// '192.168.1.24:9200',
		// 'localhost:9200'
	],
	'handlerParams'=>[
		'max_handles'=>1000
	],
	'retries'=>2,
	//搜索作品相关配置
	'opus_param'=>[
		'index'=>'poem',
		'type'=>'opus',
		'client'=>[
			'ignore'=>404,
			'timeout'=>10,
			'connect_timeout'=>10
		],
	],
	//用户搜索相关配置
	'user_param'=>[
		'index'=>'poem',
		'type'=>'user',
		'client'=>[
			'ignore'=>404,
			'timeout'=>10,
			'connect_timeout'=>10
		],
	],
	//伴奏相关搜索配置
	'poem_param'=>[
		'index'=>'poem',
		'type'=>'poem',
		'client'=>[
			'ignore'=>404,
			'timeout'=>10,
			'connect_timeout'=>10
		],
	]
];

 ?>