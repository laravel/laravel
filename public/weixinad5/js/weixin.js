var GLOBAL = {
	wxOptions:{
		title:"柳传志邀请你加入群聊",
		desc:"柳传志邀请你加入群聊中国顶级企业家俱乐部，进入可查看详情。",
		link:"http://weinidushi.com.cn/weixinad5/",
		imgUrl:"http://www.weinidushi.com.cn/weixinad5/images/share.jpg",
		type:'link',
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
					type:GLOBAL.wxOptions.type,
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
					type:GLOBAL.wxOptions.type
				});
			});
		}

	});
}
doWx();









