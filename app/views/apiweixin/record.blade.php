<!DOCTYPE html>
<html>
<head>
<?php $base_url = Config::get('app.url');?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="为你读诗-朗诵大拜年" />
	<meta name="description" content="为你读诗携全体员工，给大家拜年了" />
	<title>全民诵读大拜年</title>
	<script type="text/javascript" src="<?php echo $base_url;?>/js/sharev2/jquery.min.js"></script>
	<link href="<?php echo $base_url;?>/css/weixin.css" rel="stylesheet" type="text/css" />
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
	<div id="wxpic" class="weixinPic">
		<img width="300" border="0" height="300" src="<?php echo $base_url;?>/img/weixin_share.png">
	</div>
	<header id="header">
		<div class="header_top f55">
			录制拜年祝福
		</div>
		<div style="position:absolute;top:0px;right:5px;display:none" id="notify">
			<img src="http://weinidushi.com.cn/img/weixin/notify.png" />
		</div>
	</header>
	<section class="wrapper">
		<div class="content">
			<div class="portrait" id="static_img"><img src='<?php echo $base_url;?>/img/weixin/portrait.png' /></div>
			<div class="portrait" id="dynamic_img" style="display:none"><img src='<?php echo $base_url;?>/img/weixin/portrait.gif' /></div>
			<div class="portrait" id="start" style="display:none"><img src='<?php echo $base_url;?>/img/weixin/start.png' /></div>
			<div class="portrait" id="stop" style="display:none"><img src='<?php echo $base_url;?>/img/weixin/stop.png' /></div>
			<div class="btn">
				<input type="button" id="btn_record" style="width:280px;height:90px;background:url('<?php echo $base_url;?>/img/weixin/btn.png');background-size:280px 90px;background-repeat: no-repeat;border:0px">
				<div id = 'buttnon_group1' style="display:none">
					<input type="button" id="btn_save" style="width:280px;height:90px;background:url('<?php echo $base_url;?>/img/weixin/btn_save.png');background-size:280px 90px;background-repeat: no-repeat;border:0px">
					<input type="button" id="btn_abandon" style="width:280px;height:90px;background:url('<?php echo $base_url;?>/img/weixin/btn_abandon.png');background-size:280px 90px;background-repeat: no-repeat;border:0px">
				</div>
				<!-- <input type="button" id="btn_play" style="display:none;width:280px;height:90px;background:url('<?php echo $base_url;?>/img/weixin/btn_play.png');background-size:280px 90px;background-repeat: no-repeat;border:0px"> -->
				<input type="button" id="btn_play" style="display:none">
				<div id="saved_rerecord" style="display:none">
					<!-- <input type="button" id="savedshare" style="width:280px;height:90px;background:url('<?php echo $base_url;?>/img/weixin/saved.png');background-size:280px 90px;background-repeat: no-repeat;border:0px"> -->
					<input type="button" id="rerecord" style="margin-top:5px;margin-left:-3px;width:280px;height:90px;background:url('<?php echo $base_url;?>/img/weixin/rerecord.png');background-size:280px 90px;background-repeat: no-repeat;border:0px">
					<div style="font-size:25px;color:white;margin-top:5px;">已保存,可分享</div>
				</div>
				<!-- 正在试听 -->
				<input type="button" id="on_listen" style="display:none;margin-top:5px;margin-left:-3px;width:280px;height:90px;background:url('<?php echo $base_url;?>/img/weixin/on_listen.png');background-size:280px 90px;background-repeat: no-repeat;border:0px">
			</div>
		</div>
	</section>
	<footer id="footer">
		<div class="downtxt" style="font-size:27px;">
			<a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.ss.readpoem&g_f=991653" style="text-decoration: none;color:white">
				下载 “为你读诗”，给亲友们拜个年!
			</a>
		</div>
		<div class="bottom_logo"><img src='<?php echo $base_url;?>/img/weixin/bottom_logo.png'/></div>
	</footer>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!--<script src="../../js/m.js"></script>-->
<script>
	var GLOBAL = {
		wxOptions:{
			title:"全民诵读大拜年",
			desc:"给你拜年啦！",
			link:"http://weinidushi.com.cn/api/recordShare",
			imgUrl:"<?php echo $base_url.'/img/weixin_share.png';?>",
			type:'music',
			dataUrl:"<?php echo $base_url.'/upload/weixinvoice/';?>",
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
					  	title: GLOBAL.wxOptions.title,
					  	// desc: GLOBAL.wxOptions.desc,
					  	desc:'',
					  	link:GLOBAL.wxOptions.link,
					  	imgUrl:GLOBAL.wxOptions.imgUrl,
					  	type:'music',
					  	dataUrl:GLOBAL.wxOptions.dataUrl,
					});
					//	qq分享
					wx.onMenuShareQQ({
					    title: GLOBAL.wxOptions.title, // 分享标题
					    desc: GLOBAL.wxOptions.desc, // 分享描述
					    link: GLOBAL.wxOptions.link, // 分享链接
					    imgUrl: GLOBAL.wxOptions.imgUrl, // 分享图标
					}); 
				});
			}

		});
	}
	
	$('#btn_record').bind('click',function()
	{
		wx.startRecord({
			success:function()
			{
				$('#btn_record').css('display','none');
				$('#dynamic_img').css('display','inline');
				$('#static_img').css('display','none');
				$('#buttnon_group1').css('display','inline');
			},
	      	cancel: function () {
	        	alert('用户拒绝授权录音');
	      	}
    	});
	});
	// 保存录音
	$('#btn_save').bind('click',function()
	{
		wx.stopRecord({
		    success: function (res) {
		        var localId = res.localId;
		        wx.uploadVoice({
				    localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
				    isShowProgressTips: 1, // 默认为1，显示进度提示
			        success: function (res) 
			        {
			        	var serverId = res.serverId; // 返回音频的服务器端ID
			        	$('#btn_play').attr('data-id',localId);

			        	// 设置头像动态图片切换
			        	$('#dynamic_img').css('display','none');
			        	$('#static_img').css('display','none');

			        	// 将头像图片，换成暂停按钮
			        	$('#stop').css('display','inline');
			        	$('#start').css('display','none');

			        	$('#btn_record').css('display','none');
						$('#buttnon_group1').css('display','none');

						// 录音成功之后ajax请求上传接口
						$.ajax({
							url:'/api/weixinUploadFile',
							data:{'media_id':serverId},
							type:'post',
							async : false, //默认为true 异步
							cache:false,
							dataType:'json',
							success: function(data)
							{
								GLOBAL.wxOptions.dataUrl=data.voice_path;
								GLOBAL.wxOptions.link = 'http://weinidushi.com.cn/api/recordShare/'+data.id;
								GLOBAL.wxOptions.desc = data.username+'给你拜年啦！';
								GLOBAL.wxOptions.imgUrl= data.userportrait;
								doWx();
							},
							error:function(data)
							{
								alert('音频上传失败，请重试');
							}
						});

						// 显示提示标
						$('#notify').fadeIn("slow");
						// 将已保存可分享，和重新录制按钮显示
						$('#saved_rerecord').css('display','none');
						//将正在听显示
						$('#on_listen').css('display','inline');

						wx.playVoice({
						    localId: localId,
						});

						// 监听播放完毕接口
						wx.onVoicePlayEnd({
						    success: function (res) {
						        var localId = res.localId; // 返回音频的本地ID
						        // 隐藏正在听
						        $('#on_listen').css('display','none');

						        $('#stop').css('display','none');
			        			$('#start').css('display','inline');

			        			// 显示重新录制，已保存，克分享
			        			 $('#saved_rerecord').css('display','inline');
						    }
						});

			    	}
				});
		    }
		});
	});

	// 警告框点击消失
	$('#notify').bind('click',function(){
		$(this).fadeOut('flow');
	});
	// 放弃录音
	$('#btn_abandon,#rerecord').bind('click',function()
	{
		wx.stopRecord();
		// 将播放按钮隐藏
		$('#start').css('display','none');
		// 将重新录制，可分享隐藏
		$('#saved_rerecord').css('display','none');
		// 设置头像动态图片切换
    	$('#dynamic_img').css('display','none');
    	$('#static_img').css('display','inline');

		$('#btn_record').css('display','inline');
		$('#buttnon_group1').css('display','none');
	});

	// 播放录音
	$('#start').bind('click',function()
	{
		localId = $('#btn_play').attr('data-id');
		// 设置头像动态图片切换
    	// $('#static_img').css('display','none');
    	// $('#dynamic_img').css('display','inline');
    	$('#stop').css('display','inline');
    	$('#start').css('display','none');

    	// 隐藏重新录制，正在听按钮
    	$('#saved_rerecord').css('display','none');
    	// 显示正在听
    	$('#on_listen').css('display','inline');

		wx.playVoice({
		    localId: localId
		});
		// 监听播放完毕接口
		wx.onVoicePlayEnd({
		    success: function (res) {
		        var localId = res.localId; // 返回音频的本地ID
		        $('#static_img').css('display','none');
    			$('#dynamic_img').css('display','none');

    			$('#stop').css('display','none');
    			$('#start').css('display','inline');

    			// 隐藏正在听，显示重新录制，已保存按钮
    			$('#on_listen').css('display','none');
    			$('#saved_rerecord').css('display','inline');
		    }
		});
	});

	// 暂停录音
	$('#stop,#on_listen').bind('click',function(){
		var localId = $('#btn_play').attr('data-id');
		$('#stop').css('display','none');
    	$('#start').css('display','inline');

    	// 显示正在听
    	$('#on_listen').css('display','none');
    	// 隐藏重新录制，正在听按钮
    	$('#saved_rerecord').css('display','inline');
    	
    	wx.pauseVoice({
		    localId:localId,
		});

	});

	$(function(){
		// 判断是否为微信
		if(!isWeiXin())
		{
			document.write('只能在微信中打开');
			return;
		}
		doWx();
	});

	function isWeiXin(){
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