<?php $poem_url = Config::get('app.poem_url');?>
<?php $bg_img = $poem_url.'/upload/img/sharev2/portrait.png';?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0" />
	<link rel="stylesheet" type="text/css" href="{{$poem_url.'/upload/css/sharev2/share.css'}}">
	<script type="text/javascript" src="{{$poem_url.'/upload/js/sharev2/jquery.min.js'}}"></script>
	<script type="text/javascript" src="{{$poem_url.'/upload/js/sharev2/jquery.jplayer.min.js'}}"></script>
	<script type="text/javascript" src="{{$poem_url.'/upload/js/sharev2/jquery.jplayer.lyric.js'}}"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<title>为你诵读</title>
</head>
<body>
<textarea id="lrc_content" style="display:none;">
{{$list['content']}}
</textarea>
	<div class='body_back'>
		<div id="jquery_jplayer_1"></div>
		<div id="jp_container_1">
			<div class='portrait' style="background-image:url('{{$bg_img}}');background-repeat:no-repeat;background-size:230px 230px;">
				<div class="play">
					<a href="#" class="jp-play"><img id="play" src="{{$poem_url.'/upload/img/sharev2/play.png'}}" style="widht:55px;height:55px;margin-top:165px;" border="0"/></a>
					<a href="#" class="jp-pause"><img id="play" src="{{$poem_url.'/upload/img/sharev2/pause.png'}}" style="widht:55px;height:55px;margin-top:165px;" border="0"/></a>
				</div>
			</div>
			<!-- 进度条开始 -->
			<div class="progress_border">
				<span class="jp-current-time"></span>
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<span class="jp-duration"></span>
			</div>
			<!-- 进度条结束 -->

			<div class="poem_info">
				<span class="poem_title">
					<?php if(mb_strlen($list['name'])>15){ echo mb_substr($list['name'],0,15,'utf8').'...';}else {echo $list['name'];}?>					
				</span>
				<span class="poem_user">诵读导师：{{$list['readername']}}</span>
				<div class="mark_line">
				</div>
				<div style="height:9px;width:10px"></div>
				<div class="content">
					<ul id="lrc_list">
						加载歌词……
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class='body_footer' style='margin-top:10px'>
		<a id="downringtone"  href="{{$list['downurl']}}" target="_blank">
			<input type="button" class="btn_left" id="down_opus" value="我也来读一段" />
			&nbsp;
			<input type="button" class="btn_right" id="down_soft" value="下载为你诵读" />
		</a>
	</div>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#jquery_jplayer_1").jPlayer({
 			ready:function(){
 				$.lrc.init($('#lrc_content').val());
 				$(this).jPlayer("setMedia", {
					title: "Bubble",
					mp3: "<?php echo $list['yurl'];?>",
				}).jPlayer("play");
 			},
 			timeupdate: function(event) {
				if(event.jPlayer.status.currentTime==0){
					time = 0;
				}else {
					time = event.jPlayer.status.currentTime;
				}
			},
			play: function(event) {
				//点击开始方法调用lrc.start歌词方法 返回时间time
				if($('#lrc_content').val()!=="" || $('#lrc_content').val() != 'undefined' || $("#lrc_content").val() != null){
					$.lrc.start(function(){
						return time;
					});
				}else{
				 $(".content").html("暂无诗文内容");
				}
			},
			swfPath: "js",
			supplied: "mp3",
			wmode: "window",
			smoothPlayBar: true,
			keyEnabled: true,
			remainingDuration: true,
			toggleDuration: true
 		});
 	});
</script>
<script>
	var GLOBAL = {
		wxOptions:{
			title:"<?php echo $list['name']?>",
			desc:"<?php echo '范读导师:'.$list['readername'];?>",
			// link:"http://weinidushi.com.cn/api/record",
			imgUrl:"<?php echo 'http://weinidushi.com.cn/img/weixin/logo.png'?>",
			type:'music',
		    dataUrl:"<?php echo $list['yurl'];?>",
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
					  	type:'music',
					  	dataUrl:GLOBAL.wxOptions.dataUrl,
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
					  	type:'music',
					  	dataUrl:GLOBAL.wxOptions.dataUrl,
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