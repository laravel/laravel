<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
require_once 'admin_routes.php';
require_once 'ad_routes.php';
//require_once 'test_routes.php';
Route::get('/','Sharev2Controller@shareSoftWarev2');
Route::get('/privacy',function(){
	return View::make('ios.privacy');
});
//路由组,统一签名过滤器
// Route::group(array('before'=>'signcheck'),function(){
Route::group(array('before'=>''),function(){
	//用户通过手机号获取验证码
	Route::post('/api/sendSMS','ApiSendSMSController@sendSMS');
	//删除作品
	Route::post('/api/delOpus','ApiOpusController@delOpus');
	//用户反馈
	Route::post('/api/feedBack','ApiFeedbackController@feedBack');
	//获取奖状 
	Route::post('/api/getDiploma','ApiBillbordController@getDiploma');
	
	//用户登录接口
	Route::post('/api/login','ApiUserController@login');
	//api配置文件
	Route::post('/api/getConfig','ApiConfigController@getConfig');
	//用户注册接口
	Route::post('/api/register','ApiUserController@register');
	//active_user
	Route::post('/api/addActiveUser','ApiUserController@addActiveUser');
	//用户注册第二版
	Route::post('/api/registerV2','ApiUserController@registerV2');
	//用户第三方登录
	Route::post('/api/thirdPartLogin','ApiUserController@thirdPartLogin');
	//微信登录设置state
	Route::post('api/getWeiXinState/','ApiUserController@getWeiXinState');
	//微信登录获取access_token
	Route::post('api/getWeiXinUserInfo/','ApiUserController@getWeiXinUserInfo');
	//用户通过手机 or 邮箱获取验证码
	Route::post('/api/passwordRetake','ApiUserController@passwordRetake');
	
	//修改个人信息
	Route::post('/api/editPersonInfo','ApiUserController@editPersonInfo');
	//获取他人信息
	Route::post('/api/getOtherInfo','ApiUserController@getOtherInfo');
	//设置背景图片
	Route::post('/api/setBgPic','ApiUserController@setBgPic');
	//绑定手机号
	Route::post('/api/bindPhoneNum','ApiUserController@bindPhoneNum');
	//绑定手机号
	Route::post('/api/bindPhoneNumV2','ApiUserController@bindPhoneNumV2');
	//极光id和用户id绑定
	Route::post('/api/bindJId','ApiUserController@bindJId');
	//获取通讯录好友列表
	Route::post('/api/getBindList','ApiUserController@getBindList');
	Route::post('/api/getBindList_v2','ApiUserController@getBindList_v2');
	//第三方登陆，只修改密码
	Route::post('/api/thirdPartEditPass','ApiUserController@thirdPartEditPass');



	//获取第三方广告列表
	Route::post('/api/getThirdAdvList','ApiThirdAdvertisingController@getThirdAdvList');
	

	/**************************天籁商城**************************/
	//获取天籁商城导航列表
	Route::post('/api/getTianLaiNav','ApiTianLaiGoodsControllers@getTianLaiNav');
	// 获取天籁商城商品列表
	Route::post('/api/getTianLaiGoodsList','ApiTianLaiGoodsControllers@getTianLaiGoodsList');
	//获取天籁商城商品详情
	Route::post('/api/getTianlaiGoodsInfo','ApiTianLaiGoodsControllers@getTianlaiGoodsInfo');
	//插入天籁商城订单
	Route::post('/api/insertTianLaiOrder','ApiTianLaiGoodsControllers@insertTianLaiOrder');
	//鲜花兑换
	Route::post('/api/exchangeGood','ApiTianLaiGoodsControllers@exchangeGood');
	//判断一个人是否为会员
	Route::post('/api/isMember','ApiUserCenterController@isMember');

	/*****************************用户获取订单相关信息**************/
	//获取订单列表
	Route::post('/api/myOrderList','ApiOrderListController@myOrderList');
	// 获取订单详情
	Route::post('/api/orderInfo','ApiOrderListController@orderInfo');
	//获取订单号
	// Route::post('/api/genOrderid','ApiTianLaiGoodsControllers@genOrderid');
	//删除订单
	Route::post('/api/delMyOrder','ApiOrderListController@delMyOrder');

	/*****************************我的账单**************************/
	//获取我的账单头部导航
	Route::post('/api/getAccountStatementNav','ApiAccountStatementController@getAccountStatementNav');
	// 钻石明细
	Route::post('/api/diamondDetailList','ApiAccountStatementController@diamondDetailList');
	// 鲜花明细
	Route::post('/api/flowerDetailList','ApiAccountStatementController@flowerDetailList');

	/******************************会员权限*******************************/
	// 获取会员权限列表
	Route::post('/api/memberPermissionList','ApiUserPermissionController@memberPermissionList');
	//获取会员权限详细信息
	Route::post('/api/getMemberPerssionDetail','ApiUserPermissionController@getMemberPerssionDetail');
	//获取用户拥有的权限
	Route::post('/api/permission_config','ApiUserPermissionController@permission_config');

	//获取好友列表（相互关注）
	Route::post('/api/getFriends','ApiShareRangeController@getFriends');
	//修改自己作品公开范围
	Route::post('/api/modifyShareRange','ApiShareRangeController@modifyShareRange');

	//==============================
	//环信-验证聊天室房间
	Route::post('/api/auditRoomPwd','ApiEasemobController@auditRoomPwd');
	//环信-验证发私信权限
	Route::post('/api/auditNode','ApiEasemobController@auditNode');
	//环信token
	Route::get('/api/getToken','ApiEasemobController@getToken');
	//观众报名
	Route::post('/api/addAudience','ApiJuryController@addAudience');

	// 微信录音接口
	Route::get('/api/record','ApiWeiXinController@record');
	// 微信验证接口
	Route::get('/api/getSignPackage','ApiWeiXinController@getSignPackage');
	// 微信上传作品接口
	Route::post('/api/weixinUploadFile','ApiWeiXinController@weixinUploadFile');
	// 微信分享接口
	Route::get('/api/recordShare/{id}','ApiWeiXinController@recordShare')->where('id','[0-9]+');
	// 获取微信access_token
	Route::get('/api/getWeiXinAccess','ApiWeiXinController@getWeiXinAccess');
	//微信跳转
	Route::get('/api/weixinLogin','ApiWeiXinController@weixinLogin');

	//打赏 -- 获取随机金额
	Route::post('/api/getRandomMoney','ApiRewardController@getRandomMoney');

	//诗文比赛--添加诗文
	Route::post('/api/addOpusPoetry','ApiPoetryController@addOpusPoetry');
	//诗文比赛 -- 获取诗文比赛作品列表
	Route::post('/api/getOpusPoetryList','ApiPoetryController@getOpusPoetryList');
	//诗文比赛 -- 诗文比赛作品查看
	Route::post('/api/viewOpusPoetry','ApiPoetryController@viewOpusPoetry');
	//诗文比赛  -- 删除诗文比赛作品
	Route::post('/api/delOpusPoetry','ApiPoetryController@delOpusPoetry');
	//诗文比赛--诗文比赛转发数增加
	Route::post('/api/repostOpusPoetry','ApiPoetryController@repostOpusPoetry');
	//诗文比赛--诗文比赛赞功能
	Route::post('/api/praiseOpusPoetry','ApiPoetryController@praiseOpusPoetry');
	//诗文比赛--添加评论
	Route::post('/api/opusPoetryComment','ApiPoetryController@opusPoetryComment');
	//诗文比赛-获取诗文比赛作品评论列表
	Route::post('/api/getOpusPoetryCommentList','ApiPoetryController@getOpusPoetryCommentList');
	//诗文比赛-删除诗文作品评论
	Route::post('/api/delOpusPoetryComment','ApiPoetryController@delOpusPoetryComment');
	//诗文比赛--获取自己诗文列表
	Route::post('/api/getSelfOpusPoetry','ApiPoetryController@getSelfOpusPoetry');

	//培训班--添加表单
	Route::post('/api/joinClassActive','ApiClassActiveController@joinClassActive');
	//获取用户原来提交的表单信息
	Route::post('/api/getClassActiveUserInfo','ApiClassActiveController@getClassActiveUserInfo');
	//根据活动id获取活动详情
	Route::post('/api/getClassActiveInfo','ApiClassActiveController@getClassActiveInfo');

	//得到伴奏信息
	Route::post('/api/getPoemUserInfo','ApiPoemController@getPoemUserInfo');

	//个人中心
	Route::post('/api/userCenter','ApiUserCenterController@userCenter');
	//新功能
		// 作品/伴奏收花列表
	Route::post('/api/opusFlower','ApiUserFlowersController@opusFlower');
	//用户赠送鲜花
	Route::post('/api/giveFlowers','ApiUserFlowersController@giveFlowers');
	//用户守护榜
	Route::post('/api/getFlowers','ApiUserFlowersController@getFlowers');
	//主播总榜
	Route::post('/api/allFlowers','ApiUserFlowersController@allFlowers');
	//年榜
	Route::post('/api/YearFlowers','ApiUserFlowersController@YearFlowers');
	//月榜
	Route::post('/api/MonthFlowers','ApiUserFlowersController@MonthFlowers');
	//周榜
	Route::post('/api/WeekFlowers','ApiUserFlowersController@WeekFlowers');
	//个人送花详情
	Route::post('/api/userFlowersList','ApiUserFlowersController@userFlowersList');
	//个人收花总数
	Route::post('/api/allGetFlowers','ApiUserFlowersController@allGetFlowers');
	//个人现有鲜花钻石及花费的鲜花钻石
	Route::post('/api/costList','ApiUserFlowersController@costList');
	//榜单列表
	Route::post('/api/rank_list','ApiUserFlowersController@rank_list');
	//静态榜单内容
	Route::post('/api/rank_info','ApiUserFlowersController@rank_info');

	//作品置顶
	Route::post('/api/totop','ApiOpusController@totop');
	//添加地址
	Route::post('/api/addUserAddress','ApiAddressController@addUserAddress');
	//地址列表
	Route::post('/api/listUserAddress','ApiAddressController@listUserAddress');
	//修改地址列表
	Route::post('/api/updateUserAddress','ApiAddressController@updateUserAddress');
	//执行修改地址
	Route::post('/api/doUpdateUserAddress','ApiAddressController@doUpdateUserAddress');
	//删除地址		
	Route::post('/api/delUserAddress','ApiAddressController@delUserAddress');
	//设置默认地址
	Route::post('/api/topUserAddress','ApiAddressController@topUserAddress');
	//得到默认地址
	Route::post('/api/oldUserAddress','ApiAddressController@oldUserAddress');    
	//上传草稿箱作品
	Route::post('/api/uploadDraft','ApiDraftController@uploadDraft');
	//草稿箱列表
	Route::post('/api/getDraftList','ApiDraftController@getDraftList');
	//删除草稿
	Route::post('/api/delDraft','ApiDraftController@delDraft');
	 
	//得到草稿信息
	Route::post('/api/DraftIdGetInfo','ApiDraftController@DraftIdGetInfo');
	//将草稿发布
	Route::post('/api/toOpus','ApiDraftController@toOpus');
	//得到个人作品分类
	Route::post('/api/getClassify','ApiClassifyController@getClassify');
	//得到个人作品
	Route::post('/api/getOpus','ApiClassifyController@getOpus');
	//商城广告
	Route::post('/api/listAbShop','ApiNavigationController@listAbShop');
	//记录下载信息
	Route::post('/api/addDown','ApiDownController@addDownInfo');
	//下载信息列表
	Route::post('/api/listDown','ApiDownController@showDownInfo');
	//删除下载信息
	Route::post('/api/delDown','ApiDownController@delDownOne');
	//下载提示信息
	Route::post('/api/down_massage','ApiDownController@down_massage');
	//得到学院(诗学院颂学院等)
	Route::post('/api/getCollege','ApiCollegeController@getCollege');
	//得到学院下的年级(诗学院颂学院等)
	Route::post('/api/getGrade','ApiCollegeController@getGrade');
	//得到学院下的老师(诗学院颂学院等)
	Route::post('/api/getGradeTeacher','ApiCollegeController@getGradeTeacher');
	//得到学院下的班级列表(诗学院颂学院等)
	Route::post('/api/getClass','ApiCollegeController@getClass');
	//得到班级老师(诗学院颂学院等)
	Route::post('/api/getClassTeacher','ApiCollegeController@getClassTeacher');
	//得到班级信息
	Route::post('/api/getClassInfo','ApiCollegeController@getClassInfo');
	//申请提现功能
	Route::match(array('GET','POST'),'/api/getUserUnionid','ApiWeiXinMoneyController@getUserUnionid');
	Route::match(array('GET','POST'),'/api/checkUser','ApiWeiXinMoneyController@checkUser');


});

//修改密码
Route::post('/api/modifyPass','ApiUserController@modifyPass');



//首页面
Route::post('/api/navigationList','ApiNavigationController@navigationList');
//检查版本更新
Route::post('/api/version','ApiNavigationController@version');
//根据分类查找人的列表---主播类型16:推荐17:男18:女19:明星20:草根主播
Route::post('/api/getAnchor','ApiNavigationController@getAnchor');
//根据分类获取作品列表
Route::post('/api/accordNavGetOpusList','ApiNavigationController@accordNavGetOpusList');
//获取广告列表
Route::post('/api/getAdversing','ApiNavigationController@getAdversing'); 
//获取广告列表--新版
Route::post('/api/getAdInfo','ApiNavigationController@getAdInfo'); 
//获取闪图
Route::post('/api/showBootPic','ApiNavigationController@showBootPic');
//相册上传图片
Route::post('/api/uploadAlbum','ApiAlbumController@uploadAlbum');
//相册删除图片
Route::post('/api/delAlbum','ApiAlbumController@delAlbum');
//获取相册列表
Route::post('/api/albumList','ApiAlbumController@albumList');
//获取关注列表，歌迷列表
Route::post('/api/attentionList','ApiAttentionController@attentionList');
//获取关注列表，歌迷列表 ，修复删除分页问题
Route::post('/api/attentionListV2','ApiAttentionController@attentionListV2');
//添加关注
Route::post('/api/addAttention','ApiAttentionController@addAttention');
//取消关注
Route::post('/api/undoAttention','ApiAttentionController@undoAttention');
//移除粉丝
Route::post('/api/undoFans','ApiAttentionController@undoFans');
//拉黑
Route::post('/api/editBlackList','ApiUserController@editBlackList');
//诗人性别分类列表
Route::post('/api/getPoemerCat','ApiPoemController@getPoemerCat');
//根据性别分类查找写者
Route::post('/api/getWriterList','ApiPoemController@getWriterList');
//作品下载次数统计
Route::post('/api/poemDownNum','ApiPoemController@poemDownNum');
//根据性别分类查找读者
Route::post('/api/getReaderList','ApiPoemController@getReaderList');

//根据性别分类查找读者
Route::post('/api/getReaderList','ApiPoemController@getReaderList');


//根据伴奏id得到读者信息
Route::post('/api/getPoemUserInfo','ApiPoemController@getPoemUserInfo');
//根据读者id查找原诗
Route::post('/api/getPoemByReaderId','ApiPoemController@getPoemByReaderId');
//根据原始诗分类获取诗列表 --什么都不传，默认获取最新诗列表
Route::post('/api/getPoemListByNavId','ApiPoemController@getPoemListByNavId');
//补充新词
Route::post('api/supplementLyric','ApiPoemController@supplementLyric');
//根据原诗id获取原诗信息
Route::post('/api/accorPoemGetInfo','ApiPoemController@accorPoemGetInfo');
//作品上传
Route::post('/api/uploadOpus','ApiOpusController@uploadOpus');
//我 or 他的作品列表
Route::post('/api/getOpusList','ApiOpusController@getOpusList');
//根据作品id获取作品信息
Route::post('/api/accorOpusIdGetInfo','ApiOpusController@accorOpusIdGetInfo');
//评论作品
Route::post('/api/commentOpus','ApiOpusCommentController@commentOpus');
//作品评论列表
Route::post('/api/getCommentList','ApiOpusCommentController@getCommentList');
//删除作品评论
Route::post('/api/delOpusComment','ApiOpusCommentController@delOpusComment');
//作品收藏 or 取消收藏
Route::post('/api/colEdit','ApiCollectionController@colEdit');
//获取收藏列表
Route::post('/api/colList','ApiCollectionController@colList');
//增加/取消赞
Route::post('/api/praiseEdit','ApiPraiseController@praiseEdit');
//收听作品
Route::post('api/opusListen','ApiOpusController@opusListen');
//私信-发送私信
Route::post('api/sendPersonLetter','ApiPersonalLetterController@sendPersonLetter');
//私信-私信列表
Route::post('api/persinalLetterList','ApiPersonalLetterController@persinalLetterList');
//私信－删除私信
Route::post('api/delPersinalLetter','ApiPersonalLetterController@delPersinalLetter');
//私人定制列表
Route::post('api/getPCList','ApiPersonalCustomController@getPCList');
//私人定制中删除自己转发，或者自己的作品
Route::post('api/delPCOpus','ApiPersonalCustomController@delPCOpus');
//搜索
Route::post('api/search','ApiSearchController@search');

Route::post('api/collegeSearch','ApiSearchController@collegeSearch');


//消息列表
Route::post('api/getNotificationList','ApiNotificationController@getNotificationList');
//获取消息列表 -- version2.0
Route::post('api/getNotificationListV2','ApiNotificationController@getNotificationList');
//删除消息
Route::post('api/delNotification','ApiNotificationController@delNotification');
//标记某条消息为已读
Route::post('api/isReadedStatus','ApiNotificationController@isReadedStatus');
//获取消息数量
Route::post('api/getNotificationNum','ApiNotificationController@getNotificationNum');
//获取消息数量 --version2.0
Route::post('api/getNotificationNumV2','ApiNotificationController@getNotificationNum');
//发送短信接口
//Route::get('api/sendMsg','ApiNoteSendController@sendMsg');
//第三方转发成功，人，作品转发数+1
Route::post('/api/successShareInNum','ApiOpusController@successShareInNum');
//获取精品推荐列表
Route::post('/api/recommendation','ApiNavigationController@recommendation');
//报名读诗用户
Route::post('/api/signUpUser','ApiUserController@signUpUser');
//诗友会获取分类
Route::post('/api/getCompCategory','ApiCompetitionController@getCompCategory');
//诗友会获取子分类列表
Route::post('/api/getSubComCategory','ApiCompetitionController@getSubComCategory');
//根据子分类列表获取列表下的作品
Route::post('/api/getSubCatOpusList','ApiCompetitionController@getSubCatOpusList');
//诗友会获取某时间静态榜单列表
Route::post('/api/getStaticCompLog','ApiCompetitionController@getStaticCompLog');
//诗友会获取特定榜单作品列表
Route::post('/api/getStaticSubCatOpusList','ApiCompetitionController@getStaticSubCatOpusList');
//诗友会 根据比赛id获取比赛详情
Route::post('/api/getCompDetail','ApiCompetitionController@getCompDetail');
//诗友会 根据比赛id获取比赛详情
Route::post('/api/getCompDetailV2','ApiCompetitionController@getCompDetailV2');
//判断活动是否交费，活动是否参加过，活动是否过期
Route::post('/api/check_competion','ApiCompetitionController@check_competion');
//判断活动是否交费，活动是否参加过，活动是否过期
Route::post('/api/check_competionv2','ApiCompetitionController@check_competionv2');
//作品导航列表,主播导航列表
Route::post('/api/getBillNav','ApiBillbordController@getBillNav');
//获取作品排序列表,主播排序列表
Route::post('/api/getSubBillNav','ApiBillbordController@getSubBillNav');
// 获取作品或者博主人的列表
Route::post('/api/getOpusUserList','ApiBillbordController@getOpusUserList');
//举报作品
Route::post('/api/report','ApiOpusController@report');
//用户认证
Route::post('/api/author','ApiAuthorController@author');
//获取商品信息
Route::post('/api/getGoodsInfo','ApiPayController@getGoodsInfo');
//获取订单状态
Route::post('/api/getOrderStatus','ApiPayController@getOrderStatus');
//获取赛事
Route::post('/api/getMatchList','ApiCompetitionController@getMatchList');
//获取赛事评委
Route::post('/api/getMatchJury','ApiJuryController@getMatchJury');
//赛事提交报名表单
Route::post('/api/addMatch','ApiCompetitionController@addMatch');


//提交朗诵会表单
Route::post('/api/addLeague','ApiPayController@addLeague');
//提交夏青杯
Route::post('/api/addSummerCup','ApiPayController@addSummerCup');
//支付下单
Route::post('/api/insertOrder','ApiPayController@insertOrder');
//银联支付回调
Route::post('/api/callUnipay','ApiPayController@callUnipay');
//支付宝回调
Route::post('/api/callAlipay','ApiPayController@callAlipay');
//支付宝网页回调
Route::post('/api/callAlipayWap','ApiPayController@callAlipayWap');
//支付宝网页回调-客户端显示
Route::get('/api/callBackWap','TestController@callBackWap');
//微信支付回调接口
#Route::get('/api/callWeiXin','TestController@callWeiXin');
Route::match(array('GET','POST'),'/api/callWeiXin','TestController@callWeiXin');
//评委列表
Route::post('/api/getJuryList','ApiJuryController@getJuryList');

//朗诵会会员列表
Route::post('/api/getLeagueList','ApiLeagueController@getLeagueList');

//诗经板块列表
Route::post('/api/getShiJingList','ApiPoemController@getShiJingList');

//测试
Route::get('/api/test','ApiPayController@test');
//测试
Route::get('/api/ceshi','TestController@ceshi');
Route::get('/api/huanxin','TestController@addUsers');
//获取城市列表
Route::post('/api/getCity','ApiSearchController@getCity');
//静态榜单内容
Route::post('/api/ceshi111','ApiUserFlowersController@ceshi');
//测试
Route::post('/api/weixin','ApiWeiXinMoneyController@weixin1');
Route::post('/api/weixin1','ApiWeiXinMoneyController@get_alluser');


