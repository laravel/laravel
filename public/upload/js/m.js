var GOLBAL = {
		wxOptions:
		{
			title:$('title').html(),
			// title:'为你读诗',
			desc:$('meta[name="description"]').attr('content'),
			// desc:'为你读诗给您拜年',
			imgUrl:$('.weixinPic img').attr('src')
			// imgUrl:'http://weinidushi.com.cn/img/weixin/wxshare.png'
		}
};
function doWx()
{
	var timestamp,nonceStr,signature;
	$.ajax({
		url:"http://weinidushi.com.cn/ApiWeiXin/getSignPackage?callback=?",
		dataType:"jsonp",
		async:true,
		data:{url:encodeURIComponent(window.location.href.split('#')[0])},
		success:function(data)
		{
			timestamp = data.timestamp;
			nonceStr = data.nonceStr;
			signature = data.signature;
			wx.config({
				debug: true,
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
				wx.checkJsApi({
				  jsApiList: [
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
				//分享给朋友
				wx.onMenuShareAppMessage({
				  title: GOLBAL.wxOptions.title,
				  desc: GOLBAL.wxOptions.desc,
				  imgUrl: GOLBAL.wxOptions.imgUrl
				});
				//分享到朋友圈
				wx.onMenuShareTimeline({
				  title: GOLBAL.wxOptions.title,
				  imgUrl: GOLBAL.wxOptions.imgUrl
				});

			})
		}
	});
}

$(document).ready(function(){
	//默认微信分享
	doWx();

	$('#btn_record').click(function()
	{
		wx.startRecord({
			success:function()
			{
				alert("开始录音");
			},
	      	cancel: function () {
	        	alert('用户拒绝授权录音');
	      	}
    	});
	});
});