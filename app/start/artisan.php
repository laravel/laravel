<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/
//检测联合会会员一个月，半个月 7天 3天后是否到期，推送，发消息
Artisan::add(new UserPermissionCheck);
//会员到期后，取消相应权限
Artisan::add(new UndoUserPermission);
//将赞表(praise)表中的用户导入redis
Artisan::add(new ComImportPraise);
//将粉丝导入redis fans:1 对应follow表中fid
Artisan::add(new ImportFans);
//后台计划任务--导入伴奏
Artisan::add(new CronImportPoem);
//后台计划任务 -- 修改伴奏相关命令
Artisan::add(new CronPoem);
//后台计划任务 -- 导航-作品表拆分
Artisan::add(new ImportNavOpus);
//后台计划任务 --主播大赛,作品大赛
Artisan::add(new CronCreateBillBord);
//后台计划任务 -- 有月榜的赛事生成月榜，每月1号0点执行
Artisan::add(new CronCreateMonthComp);
//后台计划任务 -- 比赛结束计划任务
Artisan::add(new CronCreateFinishComp);
//后台计划任务 --删除CDN缓存文件
Artisan::add(new CronDelOpusCDN);
//后台计划任务 --删除非活跃用户，一周内
Artisan::add(new CronDelWeekActiveUser);
//后台计划任务 --删除非活跃用户，一月内
Artisan::add(new CronDelAllActiveUser);