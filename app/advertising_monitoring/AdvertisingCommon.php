<?php
/**
 * 广告监测公共类
 * @author:wang.hongli
 * @since:2016/06/12
 * @param:cid --区分渠道 1 展示 2点击
 * @param:adid--区分广告计划　iso_1,android_1
 * 
 * ios
 *	showlink
 *	http://weinidushi.com.cn/ad/jr_monitorShowLink?adid=ios_1&cid=1&idfa=__IDFA__&os=__OS__&timestamp=__TS__&ip=__IP__
 *	clicklink
 *	http://weinidushi.com.cn/ad/jr_monitorClickLink?adid=ios_1&cid=2&idfa=__IDFA__&os=__OS__&timestamp=__TS__&ip=__IP__
 *
 *	android
 *	showlink
 *	http://weinidushi.com.cn/ad/jr_monitorShowLink?adid=android_1&cid=1&androidid1=__ANDROIDID1__&os=__OS__&timestamp=__TS__&ip=__IP__
 *	clicklink
 *	http://weinidushi.com.cn/ad/jr_monitorClickLink?adid=android_1&cid=2&androidid1=__ANDROIDID1__&os=__OS__&timestamp=__TS__&ip=__IP__
 */
class AdvertisingCommon extends Eloquent{
	
	protected  $_config;
	
	function __construct(){
		//广告名称，和后台广告名称对应
		$this->_config = array(
				'ios_1'=>'IOS测试广告',
				'android_1'=>'安卓测试广告'
		);
	}
}