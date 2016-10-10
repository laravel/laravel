<?php
//广告相关路由
//今日头条查看监测链接
Route::get('/ad/jr_monitorShowLink','AdversingMonitorController@jr_monitorShowLink');
//今日头条点击链接监测
Route::get('/ad/jr_monitorClickLink','AdversingMonitorController@jr_monitorClickLink');