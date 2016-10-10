<?php $poem_url = Config::get('app.poem_url');?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0" />
	<link rel="stylesheet" type="text/css" href="{{$poem_url.'/upload/css/sharev2/share.css'}}">
	<script type="text/javascript" src="{{$poem_url.'/upload/js/sharev2/jquery.min.js'}}"></script>
	<title>为你诵读</title>
</head>
<body>
	<div class='body_back'>
		<div class="all_body">
			<div class="error_top"></div>
			<a id="downringtone" href="{{$downurl}}" target="_blank">
				<div class="soft_down">
					<span class="soft-font">下载“为你诵读”</span>
				</div>
			</a>
			<div class="soft_bottom">
				<img src="{{$poem_url.'/upload/img/sharev2/soft_bottom.png'}}" style="width:185px;height:35px;margin-left:auto;margint-right:auto"/>
			</div>
		</div>
	</div>
</body>
</html>