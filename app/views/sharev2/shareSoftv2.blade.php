<?php $poem_url = Config::get('app.poem_url');?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0" />
	<link rel="stylesheet" type="text/css" href="{{$poem_url.'/upload/css/sharev2/share.css'}}">
	<script type="text/javascript" src="{{$poem_url.'/upload/js/sharev2/jquery.min.js'}}"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<title>为你诵读</title>
</head>
<body>
	<div class='body_back'>
		<div class="all_body">
			<div class="soft_top"></div>
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
<script>
	var GLOBAL = {
		wxOptions:{
			title:"<?php echo '为你诵读 配乐诵读录音棚'?>",
			desc:"<?php echo 'K歌一样来诵读,想读什么读什么';?>",
			imgUrl:"<?php echo 'http://weinidushi.com.cn/img/weixin/logo.png'?>",
		}
	};
	function doWx()
	{
		// ajax 获取微信参数
		var timestamp,nonceStr,signature;
		$.ajax({
			url:'http://weinidushi.com.cn/api/getSignPackage',
			dataType:'json',
			async:false,
			data:{url:encodeURIComponent(window.location.href.split('#')[0])},
			success:function(data)
			{
				timestamp = data.timestamp;
				nonceStr = data.nonceStr;
				signature = data.signature;
				wx.config({
				    debug: false,
				    appId: 'wx1cda69b2ea12c74e',
				    timestamp: timestamp,
				    nonceStr: nonceStr,
				    signature: signature,
				    jsApiList: [
				      	'checkJsApi',
						'onMenuShareTimeline',
						'onMenuShareAppMessage',
						'startRecord',
						'stopRecord',
						'onVoiceRecordEnd',
						'playVoice',
						'pauseVoice',
						'stopVoice',
						'onVoicePlayEnd',
						'uploadVoice',
						'downloadVoice'
				    ]
				});

				wx.ready(function () {
					//分享给朋友
					wx.onMenuShareAppMessage({
					  	title: GLOBAL.wxOptions.title,
					  	desc: GLOBAL.wxOptions.desc,
// 					  	link:GLOBAL.wxOptions.link,
					  	imgUrl:GLOBAL.wxOptions.imgUrl,
						success:function(){
						},
					});
					//分享到朋友圈
					wx.onMenuShareTimeline({
					  	// title: GLOBAL.wxOptions.title,
					  	title:GLOBAL.wxOptions.title,
					  	//desc: GLOBAL.wxOptions.desc,
// 					  	link:GLOBAL.wxOptions.link,
					  	imgUrl:GLOBAL.wxOptions.imgUrl,
					});
					// qq
					wx.onMenuShareQQ({
					    title: GLOBAL.wxOptions.title, // 分享标题
					    desc:  GLOBAL.wxOptions.desc, // 分享描述
					    imgUrl: GLOBAL.wxOptions.imgUrl, // 分享图标
					}); 
				});
			}

		});
	}
	doWx();
</script>	
</body>
</html>