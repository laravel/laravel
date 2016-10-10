<?php 
Route::get('/admin',array(
	'before' => 'auth',
	function () {
		return View::make('layouts/adIndex');
	}
));
//后台-错误跳转
Route::match(array('GET','POST'),'/admin/defaultError',array('before'=>'auth','uses'=>'ErrorController@defaultError'));
//获取验证码
Route::get('/sdfiwjeijfis/captcha','UserController@captcha');
//后台--登录页面
Route::get('/admin/login','UserController@index');
//后台--验证用户登录
Route::post('/admin/login','UserController@loginPost');
Route::get('/admin/GetUserList',array('before'=>'auth','uses'=>'UserController@getUserList'));
//后台--用户列表xls
Route::get('/admin/getUserListXls',array('before'=>'auth','uses'=>'UserController@getUserListXls'));
//后台--修改用户名
Route::get('/admin/upUserName',array('before'=>'auth','uses'=>'UserController@upUserName'));
//后台--禁用/解禁用户
Route::post('/admin/delOrDelUser',array('before'=>'auth','uses'=>'UserController@delOrDelUser'));
//后台--增加用户
Route::get('/admin/addUserGet',array('before'=>'auth','uses'=>'UserController@addUserGet'));
//后台--增加用户视图
Route::post('/admin/addUserPost',array('before'=>'auth','uses'=>'UserController@addUserPost'));
//后台--修改童星
Route::post('/admin/userTeenager',array('before'=>'auth','uses'=>'UserController@userTeenager'));
//后台--将系统用户和reader用户关联
Route::get('/admin/relateRUser',array('before'=>'auth','uses'=>'UserController@relateRUser'));
//后台--范读导师
Route::get('/admin/readerList',array('before'=>'auth','uses'=>'UserController@readerList'));
//删除关联
Route::post('/admin/delRelUser',array('before'=>'auth','uses'=>'UserController@delRelUser'));
//添加关联
Route::post('/admin/addRelUser',array('before'=>'auth','uses'=>'UserController@addRelUser'));
//后台--获取作品列表
Route::get('/admin/opusList',array('before'=>'auth','uses'=>'OpusController@opusList'));
//后台--试听作品
Route::get('/admin/readOpus',array('before'=>'auth','uses'=>'OpusController@readOpus'));
//后台--添加作品各种数量
Route::get('/admin/addOpusNum',array('before'=>'auth','uses'=>'OpusController@addOpusNum'));
//后台--添加作品--渲染视图
Route::get('/admin/addOpus',array('before'=>'auth','uses'=>'OpusController@addOpus'));
//后台--添加作品--添加作品动作
Route::post('/admin/doAddOpus',array('before'=>'auth','uses'=>'OpusController@doAddOpus'));
//后台--根据用户名查找相关用户
Route::post('/admin/accordNickFind',array('before'=>'auth','uses'=>'OpusController@accordNickFind'));
//后台--删除 or 恢复作品
Route::post('/admin/delOrDelOpus',array('before'=>'auth','uses'=>'OpusController@delOrDelOpus'));
//后台--获取广告列表
Route::get('/admin/advertisingList',array('before'=>'auth','uses'=>'AdvertisingController@advertisingList'));
//后台--修改广告排序
Route::get('/admin/advOrderby',array('before'=>'auth','uses'=>'AdvertisingController@advOrderby'));
//后台--更新广告
Route::get('/admin/advUpdate/{id}',array('before'=>'auth','uses'=>'AdvertisingController@advUpdate'))->where('id','[0-9]*');
//后台--禁用 or 打开广告
Route::post('/admin/delOrDelAdv',array('before'=>'auth','uses'=>'AdvertisingController@delOrDelAdv'));
//后台--添加广告
Route::get('/admin/addAdvertising',array('before'=>'auth','uses'=>'AdvertisingController@addAdvertising'));
//后台--添加广告动作
Route::post('/admin/doAddAdvertising',array('before'=>'auth','uses'=>'AdvertisingController@doAddAdvertising'));
// 后台--添加商品推广图片
Route::match(array('GET','POST'),'/admin/addHeadPhoto',array('before'=>'auth','uses'=>'AdvertisingController@addHeadPhoto'));
// 后台--添加商品推广视图
Route::match(array('GET','POST'),'/admin/addHeadPhotoView',array('before'=>'auth','uses'=>'AdvertisingController@addHeadPhotoView'));
// 后台--推广图片列表
Route::match(array('GET','POST'),'/admin/HeadPhotoList',array('before'=>'auth','uses'=>'AdvertisingController@HeadPhotoList'));
// 后台--修改推广图片
Route::match(array('GET','POST'),'/admin/updHeadPhotoView/{id}',array('before'=>'auth','uses'=>'AdvertisingController@updHeadPhotoView'));
// 后台--修改推广图片
Route::match(array('GET','POST'),'/admin/updHeadPhoto',array('before'=>'auth','uses'=>'AdvertisingController@updHeadPhoto'));
//后台--删除广告
Route::post('/admin/delAdv',array('before'=>'auth','uses'=>'AdvertisingController@delAdv'));
//后台--获取导航（分类）列表
Route::get('/admin/navigationList',array('before'=>'auth','uses'=>'NavigationController@navigationList'));
//后台--获取闪图列表
Route::get('admin/getShowRootList',array('before'=>'auth','uses'=>'NavigationController@getShowRootList'));
//后台--添加闪图
Route::post('admin/addShowRoot',array('before'=>'auth','uses'=>'NavigationController@addShowRoot'));
//后台--删除闪图
Route::post('admin/delShowList',array('before'=>'auth','uses'=>'NavigationController@delShowList'));
//后台--修改导航图片
Route::post('/admin/modifyNavigation',array('before'=>'auth','uses'=>'NavigationController@modifyNavigation'));
//后台--评论列表
Route::get('/admin/getCommentList',array('before'=>'auth','uses'=>'CommentController@getCommentList'));
Route::post('/admin/getCommentList',array('before'=>'auth|csrf','uses'=>'CommentController@getCommentList'));
//后台--删除评论
Route::post('/admin/delOrDelComment',array('before'=>'auth|csrf','uses'=>'CommentController@delOrDelComment'));
//后台--获取最火作品榜
Route::get('/admin/getHotesOpusBoard',array('before'=>'auth','uses'=>'BillboardController@getHotesOpusBoard'));
//后台--获取最火歌手榜
Route::get('/admin/getHotesUserBoard',array('before'=>'auth','uses'=>'BillboardController@getHotesUserBoard'));
//后台--获取作品推荐榜
Route::get('/admin/recommendOpusBoard',array('before'=>'auth','uses'=>'BillboardController@recommendOpusBoard'));
//后台--获取歌手推荐榜
Route::get('/admin/recommendUserBoard',array('before'=>'auth','uses'=>'BillboardController@recommendUserBoard'));
//后台--修改作品推荐榜
Route::post('/admin/modifyRecommendOpus',array('before'=>'auth','uses'=>'BillboardController@modifyRecommendOpus'));
//后台--删除作品推荐榜
Route::post('/admin/delRecommendOpus',array('before'=>'auth','uses'=>'BillboardController@delRecommendOpus'));
//后台--根据用户昵称，作品id搜索作品
Route::post('/admin/searchOpus',array('before'=>'auth','uses'=>'BillboardController@searchOpus'));
//后台--添加推荐作品
Route::post('/admin/addRecommendOpus',array('before'=>'auth','uses'=>'BillboardController@addRecommendOpus'));
//后台--删除博主推荐
Route::post('/admin/delRecommendUser',array('before'=>'auth','uses'=>'BillboardController@delRecommendUser'));
//后台--修改博主推荐顺序呢
Route::post('/admin/modifyRecommendUserSort',array('before'=>'auth','uses'=>'BillboardController@modifyRecommendUserSort'));
//后台--根据搜索条件搜索推荐用户
Route::post('/admin/searchUser',array('before'=>'auth','uses'=>'BillboardController@searchUser'));
//后台--添加推荐用户
Route::post('/admin/addRecommendUser',array('before'=>'auth','uses'=>'BillboardController@addRecommendUser'));
//后台--获取伴奏列表
Route::get('/admin/poemList',array('before'=>'auth','uses'=>'PoemController@poemList'));
//后台--修改伴奏
Route::get('/admin/updatePoem',array('before'=>'auth','uses'=>'PoemController@updatePoem'));
//后台--修改伴奏
Route::post('/admin/updatePoemDo',array('before'=>'auth','uses'=>'PoemController@updatePoemDo'));
//后台--增加伴奏下载数量
Route::get('/admin/addPoemDownNum',array('before'=>'auth','uses'=>'PoemController@addPoemDownNum'));
//后台--修改伴奏名称 别名
Route::post('/admin/modifyPoemName',array('before'=>'auth','uses'=>'PoemController@modifyPoemName'));
//后台--添加伴奏--渲染视图
Route::get('/admin/adminAddPoem',array('before'=>'auth','uses'=>'PoemController@adminAddPoem'));
//后台--添加伴奏--动作
Route::post('/admin/doAdminAddPoem',array('before'=>'auth','uses'=>'PoemController@doAdminAddPoem'));
//后台--取消/修改认证状态
Route::post('/admin/userAuthStatus',array('before'=>'auth','uses'=>'UserController@userAuthStatus'));
//后台--修改或添加记录日志信息
Route::post('/admin/userAuthContent',array('before'=>'auth','uses'=>'UserController@userAuthContent'));
//后台--修改认证信息
Route::post('/admin/modifyAuthContent',array('before'=>'auth','uses'=>'UserController@modifyAuthContent'));
//后台--添加精品推荐
Route::get('/admin/addRecommendation',array('before'=>'auth','uses'=>'RecommendationController@addRecommendation'));
//后台--添加精品推荐--操作数据库
Route::post('/admin/doAddRecommendation',array('before'=>'auth','uses'=>'RecommendationController@doAddRecommendation'));
//后台--获取精品列表
Route::get('/admin/getRecommendation',array('before'=>'auth','uses'=>'RecommendationController@getRecommendation'));
//后台--删除精品推荐
Route::post('/admin/delOrDelRecommenda',array('before'=>'auth','uses'=>'RecommendationController@delOrDelRecommenda'));
//后台--检测某个父分类下是否存在某个子分类
Route::post('/admin/checkSubHeadExists',array('before'=>'auth','uses'=>'NavigationController@checkSubHeadExists'));
//后台--添加一个子分类
Route::post('/admin/addSubNavigation',array('before'=>'auth','uses'=>'NavigationController@addSubNavigation'));
//后台--修改分类排序
Route::post('/admin/modifyNavSort',array('before'=>'auth','uses'=>'NavigationController@modifyNavSort'));
//后台--删除分类
Route::post('/admin/navDelOrReplay',array('before'=>'auth','uses'=>'NavigationController@navDelOrReplay'));
//分享作品
Route::get('/admin/shareOpus/{id}','Sharev2Controller@shareOpusv2')->where('id','[0-9]+');
//分享原作品
Route::get('/admin/sharePoem/{id}','Sharev2Controller@sharePoemv2')->where('id','[0-9]+');
//分享软件
Route::get('/admin/shareSoftWare','Sharev2Controller@shareSoftWarev2');
//分享404页面
Route::get('/admin/share404','Sharev2Controller@share404');
//诗文比赛--诗文作品分享
Route::get('/admin/sharePoetry/{id}','Sharev2Controller@sharePoetry')->where('id','[0-9]+');
//后台--添加版本
Route::get('/admin/versionList',array('before'=>'auth','uses'=>'VersionController@versionList'));
//后台--添加版本动作
Route::post('/admin/doAddVersion',array('before'=>'auth','uses'=>'VersionController@doAddVersion'));
//后台--版本列表
Route::get('/admin/checkVersionList',array('before'=>'auth','uses'=>'VersionController@checkVersionList'));
//后台-佳作投稿
Route::get('/admin/addLyric',array('before'=>'auth','uses'=>'VersionController@addLyric'));
//后台-美文推荐
Route::get('/admin/addLyric2',array('before'=>'auth','uses'=>'VersionController@addLyric2'));
//后台--读诗报名用户列表
Route::get('/admin/signUp',array('before'=>'auth','uses'=>'UserController@signUp'));
//后台--删除读诗报名列表
Route::post('/admin/delSignUp',array('before'=>'auth','uses'=>'UserController@delSignUp'));
//后台-赛诗列表
Route::get('/admin/getCompetitionList',array('before'=>'auth','uses'=>'CompetitionController@getCompetitionList'));
//后台-添加赛事
Route::get('/admin/addCompetition',array('before'=>'auth','uses'=>'CompetitionController@addCompetition')); 
Route::post('/admin/addCompetition',array('before'=>'auth','uses'=>'CompetitionController@addCompetition')); 
Route::match(array('GET','POST'),'/admin/updateCompetition',array('before'=>'auth','uses'=>'CompetitionController@updateCompetition'));
Route::get('/admin/delCompetitionPic',array('before'=>'auth','uses'=>'CompetitionController@delCompetitionPic')); 

//后台-结束/开始赛事
Route::post('/admin/finishCompetition',array('before'=>'auth','uses'=>'CompetitionController@finishCompetition'));
//后台--置顶
Route::post('/admin/makeTop',array('before'=>'auth','uses'=>'CompetitionController@makeTop'));
//后台-举报作品
Route::get('/admin/reportOpus',array('before'=>'auth','uses'=>'PoemController@reportOpus'));
//后台-处理举报作品
Route::post('/admin/modifyReportOpus',array('before'=>'auth','uses'=>'PoemController@modifyReportOpus'));
//后台-认证用户
Route::get('/admin/authorUserList',array('before'=>'auth','uses'=>'UserController@authorUserList'));
//后台-认证用户通过审核
Route::post('/admin/checkAuthorUser',array('before'=>'auth','uses'=>'UserController@checkAuthorUser'));
//后台-夏青杯作品列表
Route::get('/admin/getSummCupOpusList',array('before'=>'auth','uses'=>'CompetitionController@getSummCupOpusList'));
//后台-夏青杯作品列表-xls
Route::get('/admin/summCupOpusXls',array('before'=>'auth','uses'=>'CompetitionController@summCupOpusXls'));
//后台-夏青杯用户列表
Route::match(array('GET','POST'),'/admin/getSumCupUserList',array('before'=>'auth','uses'=>'CompetitionController@getSumCupUserList'));
//后台-删除夏青杯作品
Route::post('/admin/admin_del_opus',array('before'=>'auth','uses'=>'CompetitionController@admin_del_opus'));
//后台-添加夏青杯权限用户
Route::match(array('GET','POST'),'/admin/addSumUser',array('before'=>'auth','uses'=>'CompetitionController@addSumUser'));
//后台-获取后台添加的夏青杯,诵读联盟用户列表
Route::match(array('GET','POST'),'/admin/getAdmAddUser',array('before'=>'auth','uses'=>'CompetitionController@getAdmAddUser'));
//后台-诵读联盟 申请列表
Route::get('/admin/admin_league_list',array('before'=>'auth','uses'=>'CompetitionController@admin_league_list'));
//后台-诵读联盟 申请列表-xls文件
Route::get('/admin/leagueXls',array('before'=>'auth','uses'=>'CompetitionController@leagueXls'));
//后台-诵读联盟 申请列表-xls文件
Route::get('/admin/summCupXls',array('before'=>'auth','uses'=>'CompetitionController@summCupXls'));
//后台-添加诵读联盟会员
Route::post('/admin/addLeagueUser',array('before'=>'auth','uses'=>'CompetitionController@addLeagueUser'));
//后台-诵读联盟 审核
Route::match(array('GET','POST'),'/admin/pass_league',array('before'=>'auth','uses'=>'CompetitionController@pass_league'));
//后台--修改作品收听，赞，转发数
Route::post('/admin/modify_opus_args',array('before'=>'auth','uses'=>'OpusController@modify_opus_args'));
//后台--订单列表
Route::match(array('GET','POST'),'/admin/orderList/{id}',array('before'=>'auth','uses'=>'OrderListController@orderList'))->where('id','[0-9]+');
//账单明细
Route::match(array('GET','POST'),'/admin/iamondWater',array('before'=>'auth','uses'=>'BillDetailsController@iamondWater'));
Route::match(array('GET','POST'),'/admin/flowersList',array('before'=>'auth','uses'=>'BillDetailsController@flowersList'));

//后台--订单列表-导出xls
// Route::match(array('GET','POST'),'/admin/exportXls',array('before'=>'auth','uses'=>'OrderListController@exportXls'));
//后天--订单通过审核
Route::match(array('GET','POST'),'/admin/orderAudit',array('before'=>'auth','uses'=>'OrderListController@orderAudit'));
//后台--评委列表
Route::match(array('GET','POST'),'/admin/juryList',array('before'=>'auth','uses'=>'JuryController@juryList'));
//后台-添加评委
Route::match(array('GET','POST'),'/admin/addJury',array('before'=>'auth','uses'=>'JuryController@addJury'));
//后台-删除评委
Route::match(array('GET','POST'),'/admin/delJury',array('before'=>'auth','uses'=>'JuryController@delJury'));
//后台-修改评委排序
Route::match('POST','/admin/modifyJurySort',['before'=>'auth','uses'=>'JuryController@modifyJurySort']);

//后台-诗经板块
Route::get('/admin/raceShiList',array('before'=>'auth','uses'=>'CompetitionController@raceShiList'));
//后台-诗经板块-add
Route::get('/admin/addShi',array('before'=>'auth','uses'=>'CompetitionController@addShi'));
//后台-诗经板块-del
Route::get('/admin/delShi',array('before'=>'auth','uses'=>'CompetitionController@delShi'));
//后台-赛事报名列表
Route::get('/admin/matchUsersList',array('before'=>'auth','uses'=>'CompetitionController@matchUsersList'));
Route::get('/admin/matchUsersListXls',array('before'=>'auth','uses'=>'CompetitionController@matchUsersListXls'));
//后台-朗诵会作品列表
Route::get('/admin/songOpusList',array('before'=>'auth','uses'=>'CompetitionController@songOpusList'));
//后台-赛事作品列表
Route::get('/admin/matchOpusList',array('before'=>'auth','uses'=>'CompetitionController@matchOpusList'));
Route::get('/admin/matchOpusListXls',array('before'=>'auth','uses'=>'CompetitionController@matchOpusListXls'));
//反馈意见列表
Route::get('/admin/feedBackList',array('before'=>'auth','uses'=>'VersionController@feedBackList'));
Route::get('/admin/setfeedBackStatus',array('before'=>'auth','uses'=>'VersionController@setfeedBackStatus'));
//后台-赛事线下缴费
Route::get('/admin/matchFreeAdd',array('before'=>'auth','uses'=>'CompetitionController@matchFreeAdd'));
//后台-上传xls文件
Route::get('/admin/poemXlsList',array('before'=>'auth','uses'=>'PoemController@poemXlsList'));
//后台将作品从分类中删除
Route::get('/admin/catremove',array('before'=>'auth','uses'=>'OpusController@catremove'));
//后台-聊天房间
Route::get('/admin/roomList',array('before'=>'auth','uses'=>'RoomController@roomList'));
//后台-添加聊天房间
Route::match(array('GET','POST'),'/admin/addRoom',array('before'=>'auth','uses'=>'RoomController@addRoom'));
//后台-聊天房间-成员列表
Route::get('/admin/roomUserList',array('before'=>'auth','uses'=>'RoomController@roomUserList'));
//赛事提交报名表单
Route::get('/admin/getMatchClause/{competitionid}','ShareController@getMatchClause')->where('competitionid','[0-9]+');

//后台-计划任务-用户列表
Route::match(array('GET','POST'),'/admin/planUserList',array('before'=>'auth','uses'=>'PoemController@planUserList'));
Route::get('/admin/planUserDel',array('before'=>'auth','uses'=>'PoemController@planUserDel'));
Route::get('/admin/planUserAdd',array('before'=>'auth','uses'=>'PoemController@planUserAdd'));
//后台-计划任务-配置
Route::get('/admin/planConfig',array('before'=>'auth','uses'=>'PoemController@planConfig'));
Route::get('/admin/updatePlanConfig',array('before'=>'auth','uses'=>'PoemController@updatePlanConfig'));
Route::post('/admin/updatePlanConfigDo',array('before'=>'auth','uses'=>'PoemController@updatePlanConfigDo'));
Route::get('/admin/upPlanConfigStatus',array('before'=>'auth','uses'=>'PoemController@upPlanConfigStatus'));
//后台修改伴奏首字母，拼音首字母
Route::post('/admin/modifyPoemChar',array('before'=>'auth','uses'=>'PoemController@modifyPoemChar'));
//计划任务链接
Route::get('/admin/planExec',array('before'=>'auth','uses'=>'PoemController@planExec'));
//后台-城市
Route::get('/admin/getCity',array('before'=>'auth','uses'=>'CompetitionController@getCity'));
Route::get('/admin/getArea',array('before'=>'auth','uses'=>'CompetitionController@getArea'));
//后台-计划任务-增加作品收听，转发，赞同数
Route::get('/admin/planAll',array('before'=>'auth','uses'=>'OpusController@test'));

//后台-活动列表
Route::get('/admin/activitiesList',array('before'=>'auth','uses'=>'JuryController@activitiesList'));
//后台-添加活动列表
Route::post('/admin/addActivities',array('before'=>'auth','uses'=>'JuryController@addActivities'));
//后台-观众报名列表
Route::get('/admin/audienceList',array('before'=>'auth','uses'=>'JuryController@audienceList'));
// 将文件中的禁用词语导入数据库
Route::get('/admin/import_word_to_db',array('before'=>'auth','uses'=>'AdminSensitiveWordController@import_word_to_db'));
// 将数据库中的数据导入tree文件
Route::post('/admin/import_word_to_tree',array('before'=>'auth','uses'=>'AdminSensitiveWordController@import_word_to_tree'));
//禁用词语列表
Route::get('/admin/adminSensitiveWord',array('before'=>'auth','uses'=>'AdminSensitiveWordController@adminSensitiveWord'));
// 删除禁用词语
Route::post('/admin/admDelSenWord',array('before'=>'auth','uses'=>'AdminSensitiveWordController@admDelSenWord'));
//添加禁用词
Route::post('/admin/addSensitiveWord',array('before'=>'auth','uses'=>'AdminSensitiveWordController@addSensitiveWord'));

//后台-邀请码列表
Route::get('/admin/inviteCodeList',array('before'=>'auth','uses'=>'JuryController@inviteCodeList'));
//后台-添加邀请码
Route::match(array('GET','POST'),'/admin/addInviteCode',array('before'=>'auth','uses'=>'JuryController@addInviteCode'));
//后台-删除邀请码
Route::get('/admin/delInviteCode',array('before'=>'auth','uses'=>'JuryController@delInviteCode'));
//后台-上传伴奏，范读
Route::match(array('GET','POST'),'/admin/adminViewUpPoem',array('before'=>'auth','uses'=>'PoemController@adminViewUpPoem'));
//后台-上传诗词
Route::match(array('GET','POST'),'/admin/adminViewUpLyric',array('before'=>'auth','uses'=>'PoemController@adminViewUpLyric'));
//后台-上传excel表格
Route::match(array('GET','POST'),'/admin/adminViewUpExcel',array('before'=>'auth','uses'=>'PoemController@adminViewUpExcel'));
//后台-删除要执行的excel任务
Route::get('/admin/delOrExecXls',array('before'=>'auth','uses'=>'PoemController@delOrExecXls'));
//后台-执行excel，导入伴奏
Route::get('/admin/importPoem',array('before'=>'auth','uses'=>'AdminImportController@importPoem'));
//后台修改导入伴奏执行时间
Route::get('/admin/updatePoemPlanTime',array('before'=>'auth','uses'=>'PoemController@updatePoemPlanTime'));
//后台-测试用程序
Route::match(array('GET',"POST"),'/admin/test','AdminTestController@test');
//后台消息通知，消息列表管理
Route::get('/admin/adminRoleList',array('before'=>'auth','uses'=>'AdminNotificationController@adminRoleList'));
//添加消息通知角色
Route::post('/admin/addRole',array('before'=>'csrf|auth','uses'=>'AdminNotificationController@addRole'));
//修改消息通知角色
Route::match(array('GET','POST'),'/admin/modifyRole/{id}',array('before'=>'auth','uses'=>'AdminNotificationController@modifyRole'))->where('id','[0-9]*');
Route::match(array('GET','POST'),'/admin/adminSendNotifiaction',array('before'=>'auth','uses'=>'AdminNotificationController@adminSendNotifiaction'));

//商品管理
Route::match(array('GET','POST'),'/admin/getGoodsList',array('before'=>'auth','uses'=>'AdminGoodsController@getGoodsList'));
//修改商品相关值
Route::post('/admin/modifyGoodInfo',array('before'=>'csrf|auth','uses'=>'AdminGoodsController@modifyGoodInfo'));
//添加商品
Route::match(array('GET','POST'),'/admin/addGoods',array('before'=>'auth','uses'=>'AdminGoodsController@addGoods'));
//添加商品分类
Route::match(array('GET','POST'),'/admin/addGoodCategory',array('before'=>'auth','uses'=>'AdminGoodsController@addGoodCategory'));
//修改商品分类
Route::match(array('GET','POST'),'/admin/modGoodCategory',array('before'=>'auth','uses'=>'AdminGoodsController@modGoodCategory'));
// 修改商品
Route::match(array('GET','POST'),'/admin/updateGoods/{id?}',array('before'=>'auth','uses'=>'AdminGoodsController@updateGoods'));
//修改商品
Route::match(array('GET','POST'),'/admin/updGoods',array('before'=>'auth','uses'=>'AdminGoodsController@updGoods'));
//添加结束比赛商品
Route::match(array('GET','POST'),'/admin/addFinishCompGoods',array('before'=>'auth','uses'=>'AdminGoodsController@addFinishCompGoods'));
// 结束比赛商品列表
Route::match(array('GET','POST'),'/admin/finishCompGoodsList',array('before'=>'auth','uses'=>'AdminGoodsController@finishCompGoodsList'));
//修改结束商品信息
Route::match(array('GET','POST'),'/admin/updateFinishCompGoods/{id?}',array('before'=>'auth','uses'=>'AdminGoodsController@updateFinishCompGoods'));
//修改结束商品信息
Route::match(array('GET','POST'),'/admin/doUpdateFinishCompGoods',array('before'=>'auth','uses'=>'AdminGoodsController@doUpdateFinishCompGoods'));
//添加培训班
Route::match(array('GET','POST'),'/admin/addClassActive',array('before'=>'auth','uses'=>'AdminClassActiveController@addClassActive'));
//修改培训班
Route::match(array('GET','POST'),'/admin/changeClassActive/{id}',array('before'=>'auth','uses'=>'AdminClassActiveController@changeClassActive'));
//删除培训班
Route::match(array('GET','POST'),'/admin/delClassActive/{id}',array('before'=>'auth','uses'=>'AdminClassActiveController@delClassActive'));
//培训班列表
Route::match(array('GET','POST'),'/admin/classActiveList',array('before'=>'auth','uses'=>'AdminClassActiveController@classActiveList'));
//报名学员列表
Route::match(array('GET','POST'),'/admin/applyStudentList',array('before'=>'auth','uses'=>'AdminClassActiveController@applyStudentList'));
//处理报名学员报名状态以及交费状态
Route::match(array('GET','POST'),'/admin/dealClassActiveStatudent',array('before'=>'auth','uses'=>'AdminClassActiveController@dealClassActiveStatudent'));
//今日头条广告监测列表
Route::match(array('GET','POST'),'/admin/jr_adv_list',array('before'=>'auth','uses'=>'AdminAdvMonitorController@jr_adv_list'));
//报名学员下载
Route::match(array('GET','POST'),'/admin/downStudentList',array('before'=>'auth','uses'=>'AdminClassActiveController@downStudentList'));
//将用户放入结束活动列表
Route::match(array('GET','POST'),'/admin/addUserToFinishCompetition',array('before'=>'auth','uses'=>'AdminFinishCompetitionController@addUserToFinishCompetition'));
//增加/删除用户参加决赛资格
Route::match(array('GET','POST'),'/admin/modifyComAuth',array('before'=>'auth','uses'=>'AdminFinishCompetitionController@modifyComAuth'));
//用户参加决赛列表
Route::match(array('GET','POST'),'/admin/listFinishCompetition',array('before'=>'auth','uses'=>'AdminFinishCompetitionController@listFinishCompetition'));
//审核决赛用户
Route::match(array('GET','POST'),'/admin/modifyFinalFlag',array('before'=>'auth','uses'=>'AdminFinishCompetitionController@modifyFinalFlag'));

//下载审核决赛用户
Route::match(array('GET','POST'),'/admin/listFinishdown',array('before'=>'auth','uses'=>'AdminFinishCompetitionController@listFinishdown'));


//个人主页列表
Route::match(['GET','POST'],'/admin/personalHomepage',['before'=>'auth','uses'=>'AdminPersonHomeController@personalHomepage']);
//添加个人主页列表
Route::match(['GET','POST'],'/admin/addPersonHome',['before'=>'auth','uses'=>'AdminPersonHomeController@addPersonHome']);
//启用或者禁用列表
Route::match(['POST'],'/admin/opetratorPersonHome',['before'=>'auth','uses'=>'AdminPersonHomeController@opetratorPersonHome']);
//修改个人主页列表顺序
Route::match(['POST'],'/admin/updateSort',['before'=>'auth','uses'=>'AdminPersonHomeController@updateSort']);
//ajax 获取后台列表分类
Route::match(['POST'],'/admin/getPersonHomeCategory',['before'=>'auth','uses'=>'AdminPersonHomeController@getPersonHomeCategory']);
// ajax 更新列表名称
Route::match(['POST'],'/admin/updateName',['before'=>'auth','uses'=>'AdminPersonHomeController@updateName']);
// 后台更新图标
Route::match(['POST'],'/admin/updatePersonHomeIcon/{id}',['before'=>'auth','uses'=>'AdminPersonHomeController@updatePersonHomeIcon']);

Route::match(['POST'],'/admin/updateColumn',['before'=>'auth','uses'=>'AdminPersonHomeController@updateColumn']);



//添加活跃数据信息
Route::match(array('GET','POST'),'/admin/addActive',array('before'=>'auth','uses'=>'AdminActiveController@addActive'));
//执行添加活跃信息
Route::match(array('GET','POST'),'/admin/doaddActive',array('before'=>'auth','uses'=>'AdminActiveController@doaddActive'));
//活跃信息报表
Route::match(array('GET','POST'),'/admin/Activelist',array('before'=>'auth','uses'=>'AdminActiveController@Activelist'));
//活跃信息列表
Route::match(array('GET','POST'),'/admin/listActive',array('before'=>'auth','uses'=>'AdminActiveController@listActive'));
//修改活跃信息
Route::match(array('GET','POST'),'/admin/changeActive',array('before'=>'auth','uses'=>'AdminActiveController@changeActive'));
//执行修改活跃信息
Route::match(array('GET','POST'),'/admin/dochangeActive',array('before'=>'auth','uses'=>'AdminActiveController@dochangeActive'));


//月起止时间
Route::match(array('GET','POST'),'/admin/countMonen',array('before'=>'auth','uses'=>'AdminActiveController@countMonen'));
//消费报表
Route::match(array('GET','POST'),'/admin/moneyActive',array('before'=>'auth','uses'=>'AdminActiveController@moneyActive'));
//消费报表列表
Route::match(array('GET','POST'),'/admin/moneyList',array('before'=>'auth','uses'=>'AdminActiveController@moneyList'));
//修改消费数据
Route::match(array('GET','POST'),'/admin/changeMoneyActive',array('before'=>'auth','uses'=>'AdminActiveController@changeMoneyActive'));
//修改消费数据
Route::match(array('GET','POST'),'/admin/dochangeMoneyActive',array('before'=>'auth','uses'=>'AdminActiveController@dochangeMoneyActive'));

/**************************************后台天籁商城****************************/
//后台天籁商城--添加商品
Route::match(['GET','POST'],'/admin/addTianLaiGoods',['before'=>'auth','uses'=>'AdminTianLaiGoodsController@addTianLaiGoods']);
//后台天籁商城--天籁商城商品列表
Route::match(['GET','POST'],'/admin/tianLaiGoodsList',['before'=>'auth','uses'=>'AdminTianLaiGoodsController@tianLaiGoodsList']);
//后台--天籁商城上传图片
Route::post('/admin/uploadGoodsImage',['before'=>'auth','uses'=>'AdminTianLaiGoodsController@uploadGoodsImage']);
//后台 -- 天籁商城删除图片
Route::post('/admin/delGoodsImage',['before'=>'auth','uses'=>'AdminTianLaiGoodsController@delGoodsImage']);
//后台--发布/下架商品
Route::post('/admin/publishOrDelTianLaiGoods/{id}',['before'=>'auth','uses'=>'AdminTianLaiGoodsController@publishOrDelTianLaiGoods'])->where('id','[0-9]*');
//后台--更新商品
Route::get('/admin/updateTianLaiGoods/{id}',['before'=>'auth','uses'=>'AdminTianLaiGoodsController@updateTianLaiGoods'])->where('id','[0-9]*');
//后台--更新商品changeSort
Route::post('/admin/doUpdateTianLaiGoods',['before'=>'auth','uses'=>'AdminTianLaiGoodsController@doUpdateTianLaiGoods']);

//加入or移除成员
Route::match(array('GET','POST'),'admin/addOrDelGroupUser',array('before'=>'auth','uses'=>'AdminGroupController@addOrDelGroupUser'));

/***************************************后台关注听新作抢先听第三方广告*******************************/
//后台--添加第三方广告栏位
Route::match(['GET','POST'],'/admin/addAdvertisingColumn',['before'=>'auth','uses'=>'AdminAdvertisingController@addAdvertisingColumn']);
//后台--添加第三方广告
Route::match(['GET','POST'],'/admin/addThirdAdvising',['before'=>'auth','uses'=>'AdminAdvertisingController@addThirdAdvising']);
//后台--获取第三方广告列表
Route::match(['GET','POST'],'/admin/thirdAdvisingList',['before'=>'auth','uses'=>'AdminAdvertisingController@thirdAdvisingList']);
//后台--删除广告
Route::post('/admin/delRevertThrAdv',['before'=>'auth','uses'=>'AdminAdvertisingController@delRevertThrAdv']);
//后台--更新第三方广告
Route::get('/admin/updateThrAd/{id}',['before'=>'auth','uses'=>'AdminAdvertisingController@updateThrAd'])->where('id','[0-9]*');
//后台---更新广告动作
Route::post('/admin/doUpdateThirdAdvising',['before'=>'auth','uses'=>'AdminAdvertisingController@doUpdateThirdAdvising']);



//添加群组
Route::match(array('GET','POST'),'admin/addGroup',array('before'=>'auth','uses'=>'AdminGroupController@addGroup'));
//群组了列表
Route::match(array('GET','POST'),'admin/listGroup',array('before'=>'auth','uses'=>'AdminGroupController@listGroup'));
//修改班级排序
Route::match(array('GET','POST'),'admin/changeSort',array('before'=>'auth','uses'=>'AdminGroupController@changeSort'));

//群组修改
Route::match(array('GET','POST'),'admin/changeGroup',array('before'=>'auth','uses'=>'AdminGroupController@changeGroup'));
//群组修改
Route::match(array('GET','POST'),'admin/dochangeGroup',array('before'=>'auth','uses'=>'AdminGroupController@dochangeGroup'));
//群组删除
Route::match(array('GET','POST'),'admin/delGroup',array('before'=>'auth','uses'=>'AdminGroupController@delGroup'));


//添加学院及年级
Route::match(array('GET','POST'),'/admin/addColloegeActive',array('before'=>'auth','uses'=>'AdminClassActiveController@addColloegeActive'));
//学院及年级列表
Route::match(array('GET','POST'),'/admin/listColloegeActive',array('before'=>'auth','uses'=>'AdminClassActiveController@listColloegeActive'));
//删除or恢复学院 年级
Route::match(array('GET','POST'),'/admin/delColloegeActive',array('before'=>'auth','uses'=>'AdminClassActiveController@delColloegeActive'));
//修改学院 年级
Route::match(array('GET','POST'),'/admin/changeColloegeActive',array('before'=>'auth','uses'=>'AdminClassActiveController@changeColloegeActive'));
//学院年级or 班级老师
Route::match(array('GET','POST'),'/admin/teacherActive',array('before'=>'auth','uses'=>'AdminClassActiveController@teacherActive'));
//学院年级老师添加
Route::match(array('GET','POST'),'/admin/addteacherActive',array('before'=>'auth','uses'=>'AdminClassActiveController@addteacherActive'));
//学院年级老师删除
Route::match(array('GET','POST'),'/admin/delteacherActive',array('before'=>'auth','uses'=>'AdminClassActiveController@delteacherActive'));
//班级老师列表
Route::match(array('GET','POST'),'/admin/addclassteacherActive',array('before'=>'auth','uses'=>'AdminClassActiveController@addclassteacherActive'));
//班级老师添加
Route::match(array('GET','POST'),'/admin/doaddteacher',array('before'=>'auth','uses'=>'AdminClassActiveController@doaddteacher'));

//群组成员列表
Route::match(array('GET','POST'),'admin/userGroup',array('before'=>'auth','uses'=>'AdminGroupController@userGroup'));
//群组老师列表
Route::match(array('GET','POST'),'admin/teacherGroup',array('before'=>'auth','uses'=>'AdminGroupController@teacherGroup'));


//加入or移除成员
Route::match(array('GET','POST'),'admin/addOrDelGroupUser',array('before'=>'auth','uses'=>'AdminGroupController@addOrDelGroupUser'));

//订单列表
Route::match(array('GET','POST'),'admin/AppOrderList',array('before'=>'auth','uses'=>'AdminOrderController@OrderList'));
//订单列表
Route::match(array('GET','POST'),'admin/changeOrderList',array('before'=>'auth','uses'=>'AdminOrderController@changeOrderList'));
//订单下载
Route::match(array('GET','POST'),'admin/execlOrderList',array('before'=>'auth','uses'=>'AdminOrderController@execlOrderList'));

//发红包
Route::match(array('GET','POST'),'admin/givemoney',array('before'=>'auth','uses'=>'AdminCostController@givemoney'));
 
//测试数据	
Route::match(array('GET','POST'),'/admin/cost',array('before'=>'auth','uses'=>'AdminCostController@cost'));
//测试数据


//权限管理
Route::match(array('GET','POST'),'/admin/authorityManagement',array('before'=>'auth','uses'=>'AdminAuthoritymanagementController@authorityManagement'));
Route::match(array('GET','POST'),'/admin/addFile',array('before'=>'auth','uses'=>'AdminAuthoritymanagementController@addFile'));
Route::match(array('GET','POST'),'/admin/permissionsDetail',array('before'=>'auth','uses'=>'AdminAuthoritymanagementController@permissionsDetail'));

Route::match(array('GET','POST'),'/admin/listTitle',array('before'=>'auth','uses'=>'AdminAuthoritymanagementController@listTitle'));
Route::match(array('GET','POST'),'/admin/listStatus',array('before'=>'auth','uses'=>'AdminAuthoritymanagementController@listStatus'));
Route::match(array('GET','POST'),'/admin/updatePermissions/{id}',array('before'=>'auth','uses'=>'AdminAuthoritymanagementController@updatePermissions'))->where('id','[0-9]*');
Route::match(array('GET','POST'),'/admin/updatePer',array('before'=>'auth','uses'=>'AdminAuthoritymanagementController@updatePer'));
Route::match(array('GET','POST'),'/admin/listDesc',array('before'=>'auth','uses'=>'AdminAuthoritymanagementController@listDesc'));
