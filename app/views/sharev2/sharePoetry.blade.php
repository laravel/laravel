<?php $poem_url = Config::get('app.poem_url');?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0" />
	<meta name="description" content="为你诵读分享" />
	<link rel="stylesheet" type="text/css" href="{{$poem_url.'/upload/css/sharev2/sharepoetry.css'}}">
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript" src="{{$poem_url.'/upload/js/sharev2/jquery.min.js'}}"></script>
	<title>为你诵读</title>
	<style type="text/css">
		.selfcontent {height:392px;overflow:hidden}
	</style>
</head>
<body>
	<div class='body_back'>
		<div id="jp_container_1">
			<div class="poem_info">
				<span class="poem_title"><?php if(mb_strlen($list['title'])>15){ echo '<h2>'.mb_substr($list['title'],0,15,'utf8').'...'.'</h2>';}else {echo '<h2>'.$list['title'].'</h2>';}?></span>
				<span class="poem_user">{{$list['author']}}（{{$list['nationality']}}）</span>
				<div style="height:9px;width:10px"></div>
				<div >
					<ul id="lrc_list">
						<div  id="content">{{$list['content']}}</div>
						<div style="color:#e97423;margin-top:16px">
							<img id="downicon" src="{{$poem_url.'/upload/img/sharev2/downicon.png'}}" style="width:90px;height:17px;display:none"/>
							<img id="upicon" src="{{$poem_url.'/upload/img/sharev2/upicon.png'}}" style="width:90px;height:17px;display:none"/>
						</div>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class='body_footer' style='margin-top:10px'>
		<a id="downringtone"  href="{{$list['downurl']}}" target="_blank">
			<input type="button" class="btn_left" id="down_opus" value="我也来写一首" />
			&nbsp;
			<input type="button" class="btn_right" id="down_soft" value="下载为你诵读" />
		</a>
	</div>
<script>
	//获取div高度
	var height = $('#content').height();
	if(height>394){
		$('#content').addClass('selfcontent');
		$('#downicon').css({"display":"inline"});
	}else{
		$('#content').addClass('selfcontent');
	}
	$('#downicon').bind('click',function(){
		$('#content').removeClass('selfcontent');
		$('#downicon').css({'display':'none'});
		$('#upicon').css({'display':'inline'});
		});
	$('#upicon').bind('click',function(){
		$('#content').addClass('selfcontent');
		$('#downicon').css({'display':'inline'});
		$('#upicon').css({'display':'none'});
		});
	var GLOBAL = {
		wxOptions:{
			title:"<?php echo $list['title']?>",
			desc:"<?php echo '作者：'.$list['author'];?>",
			// link:"http://weinidushi.com.cn/api/record",
			imgUrl:"<?php echo $list['sportrait'];?>",
			type:'music',
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
// 					  	type:'music',
// 					  	dataUrl:GLOBAL.wxOptions.dataUrl,
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
// 					  	type:'music',
// 					  	dataUrl:GLOBAL.wxOptions.dataUrl,
					});
					// qq
					wx.onMenuShareQQ({
					    title: GLOBAL.wxOptions.title, // 分享标题
					    desc:  GLOBAL.wxOptions.desc, // 分享描述
					    link: GLOBAL.wxOptions.link, // 分享链接
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