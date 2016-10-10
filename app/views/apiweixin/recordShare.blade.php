<!DOCTYPE html>
<html>
<head>
<?php $base_url = Config::get('app.url');?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="为你读诗-朗诵大拜年" />
	<meta name="description" content="为你读诗携全体员工，给大家拜年了" />
	<title>全民诵读大拜年</title>
	<script type="text/javascript" src="<?php echo $base_url;?>/js/sharev2/jquery.min.js"></script>
	<link href="<?php echo $base_url;?>/css/weixinshare.css" rel="stylesheet" type="text/css" />
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<script type="text/javascript">
		var _phoneWidth = parseInt(window.screen.width);
		var _phoneScale = _phoneWidth/640;
		var ua = navigator.userAgent;
		document.write('<meta name="viewport" content="width=640, target-densitydpi=device-dpi, initial-scale='+_phoneScale+', minimum-scale='+_phoneScale+', maximum-scale='+_phoneScale+', user-scalable=no">');
	</script>
	<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?9eaf373d894795c29439b0d4ca5763d8";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>



</head>
<body>
	<!-- 头部 -->
	<header>
		<a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.ss.readpoem&g_f=991653">
			<div class="header_border">
				<span class="logo"><img src="<?php echo $base_url.'/img/weixin/logo.png';?>" width=80 height=80/></span>
				<div class="header_title">
					<span class="header_title_top" style="color:black">为你读诗</span>
					<span class="header_title_foot">给您全家拜年啦！</span>
				</div>
				<div class="header_right">
					
						<span><img src="<?php echo $base_url.'/img/weixin/download2.png';?>" style="margin-top:5px"></span>
						<div class="hright_font_top">
							<span style="margin-left:61px;font-size:23px">马上下载</span>
							<span style="margin-left:61px;font-size:23px">为你读诗</span>
						</div>
				</div>
			</div>
		</a>
		
	</header>

	<!-- 中间部分 -->
	<section>
		<audio id="voice_id">
		  	<source src="<?php echo $rs['voice_path'];?>" type="audio/mpeg">
		</audio>
		<div class="box">
			<!-- 用户头像及播放条 -->
			<div class="portrait_voice" >
				<span class="userportrait"><img src="<?php echo $rs['portrait'];?>"/></span>
				<div class="dialogbox">
					<!-- 长条图片 点击播放-->
					<img src="<?php echo $base_url.'/img/weixin/dialogbox.png';?>" style="z-index:-1000"/>
					<div id="dialogbox_in1" class="dialogbox_in">
						<img class="voice_btn" src="<?php echo $base_url.'/img/weixin/voice.png';?>" />
						<div style="z-index: 10000; margin-top: -52px;margin-left:80px">
							<span style="margin-left:10px">点击收听拜年祝福！</span>
							<span style="margin-left:19px" id="second"><?php echo $rs['time'];?></span>"
						</div>
					</div>
					<div id="dialogbox_in2" class="dialogbox_in" style="display:none">
						<div style="margin-left: 31px;margin-top:-70px">
							<a style="text-decoration:none;color:#fecb95" href="http://weinidushi.com.cn/api/getWeiXinAccess">点击对话框即可录制拜年祝福！</a>
						</div>
					</div>
				</div>
			</div>

			<!-- 用户昵称，推荐话语 -->
			<div class="nick_command">
				<div style="font-size:25px"><?php echo $rs['name']?></div>
				<div style="font-size:23px">在 "为你读诗" 客户端上给你拜年啦！</div>
			</div>
		</div>
	</section>

	<!-- 底部 -->
	<footer>
		<div class="foot_box">
			<span><img src="<?php echo $base_url.'/img/weixin/bottom.png';?>" width=145 style="margin-top:6px"></span>
			<div style="display:inline;float:right;margin-top:3px">全民诵读大拜年</div>
		</div>

	</footer>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!--<script src="../../js/m.js"></script>-->
<script>
	var GLOBAL = {
		wxOptions:{
			title:"全民诵读大拜年",
			desc:"<?php echo $rs['name'];?>给你拜年啦！",
			// link:"http://weinidushi.com.cn/api/record",
			imgUrl:"<?php echo $rs['portrait'];?>",
			type:'music',
			// dataUrl:"<?php echo $base_url.'/upload/weixinvoice/';?>",
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
					  	link:GLOBAL.wxOptions.link,
					  	imgUrl:GLOBAL.wxOptions.imgUrl,
					  	type:'music',
					  	dataUrl:GLOBAL.wxOptions.dataUrl,
						success:function(){
						},
					});
					//分享到朋友圈
					wx.onMenuShareTimeline({
					  	// title: GLOBAL.wxOptions.title,
					  	title:'今年春节流行诵读大拜年！',
					  	desc: GLOBAL.wxOptions.desc,
					  	link:GLOBAL.wxOptions.link,
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
<script language="javascript">
$(function(){

	// 判断是否为微信
	// if(!isWeiXin())
	// {
	// 	document.write('只能在微信中打开');
	// 	return;
	// }
	var audio=document.getElementById("voice_id");
	var time = <?php echo $rs['time'];?>;
	$("#dialogbox_in1").click(function(){
		if(audio.paused){
			audio.play();
			// 每隔一秒执行一次
			setInterval("changeTime()",1000);
			// 修改时间
			setTimeout("changeStatus()",time*1000);
		}
	});
});
// 延迟几秒，改变状态
function changeStatus()
{
	$("#dialogbox_in1").remove();
	$('#dialogbox_in2').css('display','inline');
}

// 修改剩余时间
function changeTime()
{
	remainTime = $('#second').html();
	remainTime = remainTime-1;
	$('#second').html(remainTime);
}

// 判断是否为微信
function isWeiXin()
{
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}
</script>
</body>
</html>