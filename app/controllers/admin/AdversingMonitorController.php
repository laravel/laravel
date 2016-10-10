<?php
/**
 * 监测广告链接控制器
 * @author wang.hongli
 * @since:2016/06/12
 *
 */
class AdversingMonitorController extends BaseController{
	
	/**
	 * 今日头条展现量统计--今日头条回调地址
	 * @author:wang.hongli
	 * @since:2016/06/12
	 */
	public function jr_monitorShowLink(){
		$jinRiTouTiaoMonitoring = new JinRiTouTiaoMonitoring();
		$jinRiTouTiaoMonitoring->jr_monitorShowLink();
	}
	
	/**
	 * 今日头条点击量统计-- 今日头条回调地址
	 * @author:wang.hongli
	 * @since:2016/06/12
	 */
	public function jr_monitorClickLink(){
		$jinRiTouTiaoMonitoring = new JinRiTouTiaoMonitoring();
		$jinRiTouTiaoMonitoring->jr_monitorClickLink();
	}
}