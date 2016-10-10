<!doctype html>
<html lang='zh'>
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="<?php echo asset('css/bootstrap.min.css');?>">
    <script src="<?php echo asset('js/jquery.min.js');?>"></script>
    <script src="<?php echo asset('js/jquery-ui.js');?>"></script>
    <script src="<?php echo asset('js/bootstrap.min.js');?>"></script>
    <script src="<?php echo asset('js/jquery.cookie.js');?>"></script>
</head>
<body>
    <!--导航条开始-->
    <nav class="navbar navbar-inverse" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">为你读诗后台管理</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="#">后台信息</a></li>
                <li><a href="#">访客信息</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">快速导航<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">用户列表</a></li>
                        <li><a href="#">管理员列表</a></li>
                        <li class="divider"></li>
                        <li><a href="#">作品列表</a></li>
                        <li class="divider"></li>
                        <li><a href="#">广告列表</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="label label-danger">当前登录:admin</span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">系统设置<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">注销登录</a></li>
                        <li><a href="#">切换账号</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
    <!--导航条结束-->
    <!--警告条开始-->
    <div class="alert alert-danger alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <strong>严重警告:</strong> 强烈建议使用chrome,firefox浏览器,否则会有布局混乱现象!坚决抵制IE,360浏览器!!! 谢谢合作！:(- -
    </div>
    <!--警告条结束-->

    <!--主体部分开始-->
    <div class="row">
        <!--左边开始-->
        <div class="col-xs-12 col-sm-6 col-md-2">
            <div class="panel-group" id="accordion">
   <!--<?php  //foreach ($nav as $key => $value) {?>
            <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                <?php // echo$value['navname'] ?> <span class="badge pull-right"><?php //echo count($value['two']) ; ?></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                            <?php //foreach ($value['two'] as $k => $v){ ?>
                                          <li class="list-group-item"><a href="<?php // echo $v['url']?>"><?php // echo $v['navname']?></a></li>
                              
                            <?php //}?>
                            </ul>
                        </div>
                    </div>
                </div>
       <?php  //}?>-->
            <!--导航开始循环开始--> 
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                用户管理 <span class="badge pull-right">5</span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="/admin/GetUserList">用户列表</a></li>
                              <li class="list-group-item"><a href="/admin/addUserGet">添加用户</a></li>
                              <li class="list-group-item"><a href="/admin/signUp">范读报名</a></li>
                              <li class="list-group-item"><a href="/admin/authorUserList">认证申请</a></li>
                              <li class="list-group-item"><a href="/admin/feedBackList">意见反馈</a></li>
                              <li class="list-group-item"><a href="/admin/relateRUser">范读导师</a></li>
                              <li class="list-group-item"><a href="/admin/readerList">用户和范读导师关联</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
        <!-- 导航循环结束--> 
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                            作品管理<span class="badge pull-right">6</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse ">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/opusList'>作品列表</a></li>
                              <li class="list-group-item"><a href='/admin/addOpus'>添加作品</a></li>
                              <li class="list-group-item"><a href='/admin/reportOpus'>举报作品</a></li>
                              <li class="list-group-item"><a href='/admin/addLyric'>佳作投稿</a></li>
                              <li class="list-group-item"><a href='/admin/addLyric2'>美文推荐</a></li>
                              <li class="list-group-item"><a href='/admin/planUserList'>计划任务</a></li>
                              <li class="list-group-item"><a href='/admin/planConfig'>计划任务配置</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
                            伴奏管理<span class="badge pull-right">6</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseFive" class="panel-collapse collapse ">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="/admin/poemList">伴奏列表</a></li>
                              <li class="list-group-item"><a href="/admin/adminAddPoem">添加伴奏</a></li>
                              <li class="list-group-item"><a href='/admin/adminViewUpPoem'>上传范读伴奏</a></li>
                              <li class="list-group-item"><a href='/admin/adminViewUpLyric'>上传诗词</a></li>
                              <li class="list-group-item"><a href='/admin/adminViewUpExcel'>上传excel表格</a></li>
                              <li class="list-group-item"><a href='/admin/poemXlsList'>执行导入</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseIncome">
                            收入管理<span class="badge pull-right">5</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseIncome" class="panel-collapse collapse ">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="/admin/orderList/1">联合会会费</a></li>
                              <li class="list-group-item"><a href="/admin/orderList/2">诵读比赛费</a></li>
                              <li class="list-group-item"><a href='/admin/orderList/3'>诗文比赛费</a></li>
                              <li class="list-group-item"><a href='/admin/orderList/4'>培训班活动费</a></li>
                              <li class="list-group-item"><a href='/admin/orderList/5'>打赏团队费</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
          <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#cost">
                            支出管理<span class="badge pull-right">5</span>
                        </a>
                      </h4>
                    </div>
                    <div id="cost" class="panel-collapse collapse ">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="/admin/cost">提现管理</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#billdetails">
                            账单明细<span class="badge pull-right">5</span>
                        </a>
                      </h4>
                    </div>
                    <div id="billdetails" class="panel-collapse collapse ">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="/admin/iamondWater">钻石流水</a></li>
                              <li class="list-group-item"><a href="/admin/flowersList">鲜花流水</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#authorityManagement">
                            权限管理<span class="badge pull-right">5</span>
                        </a>
                      </h4>
                    </div>
                    <div id="authorityManagement" class="panel-collapse collapse ">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="/admin/authorityManagement">权限列表</a></li>
                               <li class="list-group-item"><a href="/admin/addFile">添加权限</a></li>
                               <li class="list-group-item"><a href="/admin/permissionsDetail">权限详情</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThirdAdv">
                            第三方投放广告<span class="badge pull-right">2</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseThirdAdv" class="panel-collapse collapse ">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/addAdvertisingColumn'>添加广告栏目</a></li>
                              <li class="list-group-item"><a href='/admin/addThirdAdvising'>添加广告</a></li>
                              <li class="list-group-item"><a href='/admin/thirdAdvisingList'>广告列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                            广告管理<span class="badge pull-right">2</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse ">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/advertisingList'>广告列表</a></li>
                              <li class="list-group-item"><a href='/admin/addAdvertising'>添加广告</a></li>
                              <li class="list-group-item"><a href='/admin/HeadPhotoList'>作品推广图片列表</a></li>
                              <li class="list-group-item"><a href='/admin/addHeadPhotoView'>添加推广图片</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
                            评论管理<span class="badge pull-right">1</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseSix" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/getCommentList'>评论列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#order">
                            订单管理<span class="badge pull-right">1</span>
                        </a>
                      </h4>
                    </div>
                    <div id="order" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/AppOrderList'>订单列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
                            榜单管理<span class="badge pull-right">5</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/getHotesOpusBoard'>最火作品</a></li>
                              <li class="list-group-item"><a href='/admin/recommendOpusBoard'>推荐作品</a></li>
                              <li class="list-group-item"><a href='/admin/getHotesUserBoard'>最火主播</a></li>
                              <li class="list-group-item"><a href='/admin/recommendUserBoard'>推荐主播</a></li>
                              <li class="list-group-item"><a href="/admin/raceShiList">诗经板块</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseGoods">
                            商品管理<span class="badge pull-right">2</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseGoods" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/addGoodCategory'>添加商品分类</a></li>
                              <!-- <li class="list-group-item"><a href='/admin/addGoods'>添加商品</a></li> -->
                              <li class="list-group-item"><a href='/admin/getGoodsList'>商品列表</a></li>
                              <li class="list-group-item"><a href='/admin/addFinishCompGoods'>添加结束比赛商品</a></li>
                              <li class="list-group-item"><a href='/admin/finishCompGoodsList'>结束比赛商品列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTianlaiGoods">
                            天籁商城商品管理<span class="badge pull-right">2</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseTianlaiGoods" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/addTianLaiGoods'>添加商品</a></li>
                              <li class="list-group-item"><a href='/admin/tianLaiGoodsList'>商品列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseClassActive">
                            培训班管理<span class="badge pull-right">3</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseClassActive" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                             <li class="list-group-item"><a href='/admin/addColloegeActive'>添加学院及年级</a></li>
                              <li class="list-group-item"><a href='/admin/listColloegeActive'>学院及年级列表</a></li>
                              <li class="list-group-item"><a href='/admin/teacherActive'>学院及年级老师</a></li>
                               <li class="list-group-item"><a href='/admin/addclassteacherActive'>班级老师</a></li>
                              <li class="list-group-item"><a href='/admin/addClassActive'>添加培训班</a></li>
                              <li class="list-group-item"><a href='/admin/classActiveList'>培训班列表</a></li>
                              <li class="list-group-item"><a href='/admin/applyStudentList'>报名学员列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFinishCompetition">
                            结束活动赛事管理<span class="badge pull-right">2</span>
                        </a>
                        
                      </h4>
                    </div>
                    <div id="collapseFinishCompetition" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/addUserToFinishCompetition'>参赛用户管理</a></li>
                              <li class="list-group-item"><a href='/admin/listFinishCompetition'>决赛用户列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseSai">
                            活动赛事管理<span class="badge pull-right">16</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseSai" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/admin_league_list'>联合会列表</a></li>
                              <li class="list-group-item"><a href='/admin/matchUsersList'>赛事报名列表</a></li>
                              <li class="list-group-item"><a href='/admin/matchOpusList'>赛事作品列表</a></li>
                              <li class="list-group-item"><a href='/admin/getCompetitionList'>活动赛事列表</a></li>
                              <li class="list-group-item"><a href='/admin/addCompetition'>添加活动赛事</a></li>
                              
                              <li class="list-group-item"><a href='/admin/songOpusList'>诵读会作品列表</a></li>
                              
                              <li class="list-group-item"><a href='/admin/addJury'>添加评委</a></li>
                              <li class="list-group-item"><a href='/admin/juryList'>评委列表</a></li>
                              
                              <li class="list-group-item"><a href='/admin/getAdmAddUser'>后台添加列表</a></li>
                              
                              <li class="list-group-item"><a href='/admin/getSummCupOpusList'>夏青杯作品列表</a></li>
                              <li class="list-group-item"><a href='/admin/getSumCupUserList'>夏青杯用户列表</a></li>
                              
                               <li class="list-group-item"><a href='/admin/matchFreeAdd'>赛事线下缴费</a></li>
                               
                               
                               <li class="list-group-item"><a href='/admin/activitiesList'>线下活动列表</a></li>
                               <li class="list-group-item"><a href='/admin/audienceList'>活动观众报名</a></li>
                               
                               <li class="list-group-item"><a href='/admin/inviteCodeList'>邀请码列表</a></li>
                               <li class="list-group-item"><a href='/admin/addInviteCode'>添加邀请码</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseZone">
                            聊天室管理<span class="badge pull-right">2</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseZone" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/addRoom'>添加聊天室</a></li>
                              <li class="list-group-item"><a href='/admin/roomList'>聊天室列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#group">
                            群组管理<span class="badge pull-right">3</span>
                        </a>
                      </h4>
                    </div>
                    <div id="group" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/addGroup'>添加群组</a></li>
                              <li class="list-group-item"><a href='/admin/listGroup'>群组列表</a></li>
                               <li class="list-group-item"><a href='/admin/userGroup'>交费学员列表</a></li>
                                <li class="list-group-item"><a href='/admin/teacherGroup'>群组老师列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                               
              
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseEight">
                                版本管理<span class="badge pull-right">3</span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseEight" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="/admin/versionList">更新版本</a></li>
                              <li class="list-group-item"><a href="/admin/checkVersionList">版本列表</a></li>
                              <li class="list-group-item"><a href="/admin/getShowRootList">闪图列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">
                            导航管理<span class="badge pull-right">1</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseSeven" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="/admin/navigationList">导航列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
               
              <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseNigh">
                            应用推荐<span class="badge pull-right">2</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseNigh" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/getRecommendation'>应用列表</a></li>
                              <li class="list-group-item"><a href='/admin/addRecommendation'>添加应用</a></li>
                            </ul>
                        </div>
                    </div>
                </div> 
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseSensitive">
                            禁用词语管理<span class="badge pull-right">1</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseSensitive" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/adminSensitiveWord'>禁用词语</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseNotification">
                            消息管理<span class="badge pull-right">1</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseNotification" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/adminRoleList'>角色管理</a></li>
                              <li class="list-group-item"><a href='/admin/adminSendNotifiaction'>发送消息</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseConfig">
                            客户端配置管理<span class="badge pull-right">1</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseConfig" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/addPersonHome'>添加个人主页条目</a></li>
                              <li class="list-group-item"><a href='/admin/personalHomepage'>个人主页显示列表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                
                 <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdvMonitor">
                            第三方广告监测<span class="badge pull-right">1</span>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseAdvMonitor" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/jr_adv_list'>今日头条监测</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                 <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#hot" href="#hot">
                            活跃度报表<span class="badge pull-right">1</span>
                        </a>
                      </h4>
                    </div>
                    <div id="hot" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                              <li class="list-group-item"><a href='/admin/Activelist'>活跃度报表</a></li>
                              <li class="list-group-item"><a href='/admin/moneyActive'>付费用户报表</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--左边结束-->

        <!--右边开始-->
        <div class="col-xs-6 col-md-10">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">搜索条件</h3>
                </div>
                <div class="panel-body">
                    @yield('search')
                </div>
                 <div class="panel-heading">
                    <h3 class="panel-title">@yield('crumbs')</h3>
                </div>
                <div class="panel-body">
                    @yield('content')
                </div>
            </div>
        </div>
        <!--右边结束-->
    </div>
    <!--主体部分结束-->
    <script>
		//读取cookie,添加类选择器in
		flag = $.cookie('admin_id_in');
		flag = '#'+flag;
		$(flag).addClass('in');
		
		$('.list-group-item').bind('click',function(data){
			//清除原来的cookie
			$.removeCookie('admin_id_in', { path: '/' });
			$.removeCookie('admin_id_in', { path:'/admin' });
			$.removeCookie('admin_id_in', { path:'/admin/orderList' });
			id = $(this).parent().parent().parent().attr('id');
			//设置新的cookie
			$.cookie('admin_id_in',id);
		});
	</script>
</body>
 
</html>