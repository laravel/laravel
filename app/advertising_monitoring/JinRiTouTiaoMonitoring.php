<?php
/**
 * 今日头条监测model
 * @author:wang.hongli
 * @since:2016/06/12
 */
class JinRiTouTiaoMonitoring extends AdvertisingCommon {
	private $_ios_rules;
	private $_android_rules;
	function __construct() {
		parent::__construct ();
		$this->_ios_rules_show = array (
				'adid' => 'required|alpha_dash',
				'cid' => 'required|in:1,2',
				'idfa' => 'required|alpha_dash|unique:jinri_statistics_show,idfa', // idfa 唯一有则不插入
// 				'mac' => 'alpha_dash',
				'os' => 'required|in:0,1,2,3', // 0–Android 1–iOS 2– WP 3- Others
				'timestamp' => 'required|integer',
				'ip' => 'ip',
		);
		$this->_ios_rules_click = array (
				'adid' => 'required|alpha_dash',
				'cid' => 'required|in:1,2',
				'idfa' => 'required|alpha_dash|unique:jinri_statistics_click,idfa', // idfa 唯一有则不插入
// 				'mac' => 'alpha_dash',
				'os' => 'required|in:0,1,2,3', // 0–Android 1–iOS 2– WP 3- Others
				'timestamp' => 'required|integer',
				'ip' => 'ip',
		);
		$this->_android_rules = array (
				'adid' => 'required|alpha_dash',
				'cid' => 'required|in:1,2',
				'androidid1' => 'alpha_dash',
// 				'mac' => 'alpha_dash',
				'os' => 'required|in:0,1,2,3',
				'timestamp' => 'required|integer',
				'ip' => 'required|ip',
		);
	}
	/**
	 * 今日头条展示监测链接
	 * 
	 * @author :wang.hongli
	 * @since :2016/06/12
	 */
	public function jr_monitorShowLink() {
		$data ['adid'] = Input::get ( 'adid' );
		if (empty ( $data ['adid'] ) || strpos ( $data ['adid'], '_' ) === false) {
			return false;
		}
		$data ['cid'] = Input::get ( 'cid' ); // 1展示 2点击
		$data ['idfa'] = Input::has ( 'idfa' ) ? Input::get ( 'idfa' ) : 0;
		$data ['androidid1'] = Input::has ( 'androidid1' ) ? Input::get ( 'androidid1' ) : 0;
// 		$data ['mac'] = Input::has ( 'mac' ) ? Input::get ( 'mac' ) : '';
		$data ['os'] = Input::get ( 'os' );
		$input_time =  Input::has ( 'timestamp' ) ? Input::get('timestamp') :time();
		$data ['timestamp'] = substr($input_time, 0,10);
		$data ['ip'] = Input::has ( 'ip' ) ? Input::get ( 'ip' ) : '127.0.0.1';
		$adid_arr = explode ( '_', $data ['adid'] );
		$adid_flag = $adid_arr [0];
		switch ($adid_flag) {
			case 'ios' :
				$validator = Validator::make ( $data, $this->_ios_rules_show );
				if ($validator->fails ()) {
					$msg =  $validator->messages()->first();
					$this->putErrorMsg($msg);
					return false;
				}
				break;
			case 'android' :
				$validator = Validator::make ( $data, $this->_android_rules );
				if ($validator->fails ()) {
					$msg =  $validator->messages()->first();
					$this->putErrorMsg($msg);
					return false;
				}
				break;
		}
		try {
			DB::table ( 'jinri_statistics_show' )->insert ( $data );
		} catch ( Exception $e ) {
			file_put_contents('bbbb.txt',json_encode($e->getMessage()));
			return false;
		}
	}
	
	/**
	 * 今日头条点击监测链接
	 * 
	 * @author :wang.hongli
	 * @since :2016/06/12
	 */
	public function jr_monitorClickLink() {
		$data ['adid'] = Input::get ( 'adid' );
		if (empty ( $data ['adid'] ) || strpos ( $data ['adid'], '_' ) === false) {
			return false;
		}
		$data ['cid'] = Input::get ( 'cid' );// 1展示 2点击
		$data ['idfa'] = Input::has ( 'idfa' ) ? Input::get ( 'idfa' ) : 0;
		$data ['androidid1'] = Input::has ( 'androidid1' ) ? Input::get ( 'androidid1' ) : 0;
// 		$data ['mac'] = Input::has ( 'mac' ) ? Input::get ( 'mac' ) : '';
		$data ['os'] = Input::get ( 'os' );
		
		$input_time =  Input::has ( 'timestamp' ) ? Input::get('timestamp') :time();
		$data ['timestamp'] = substr($input_time, 0,10);

		$data ['ip'] = Input::has ( 'ip' ) ? Input::get ( 'ip' ) : '127.0.0.1';
		$adid_arr = explode ( '_', $data ['adid'] );
		$adid_flag = $adid_arr [0];
		switch ($adid_flag) {
			case 'ios' :
				$validator = Validator::make ( $data, $this->_ios_rules_click );
				if ($validator->fails ()) {
					$msg =  $validator->messages()->first();
					$this->putErrorMsg($msg);
					return false;
				}
				break;
			case 'android' :
				$validator = Validator::make ( $data, $this->_android_rules );
				if ($validator->fails ()) {
					$msg =  $validator->messages()->first();
					$this->putErrorMsg($msg);
					return false;
				}
				break;
		}
		try {
			DB::table ( 'jinri_statistics_click' )->insert ( $data );
		} catch ( Exception $e ) {
			file_put_contents('aaaa.txt',json_encode($e->getMessage()));
			return false;
		}
	}

	protected function putErrorMsg($msg){
		$msg = !empty($msg) ? $msg : "error\n";
		$handle = fopen('jinri_error_log.txt','a+');
		fwrite($handle, $msg."\n");
		fclose($handle);
	}
}