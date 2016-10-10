<?php
/**
 ** 友盟推送
 *  @author:wang.hongli
 *  @since:2016/04/06
 */
require_once (app_path ( 'ext/notification/android/AndroidBroadcast.php' ));
require_once (app_path ( 'ext/notification/android/AndroidFilecast.php' ));
require_once (app_path ( 'ext/notification/android/AndroidGroupcast.php' ));
require_once (app_path ( 'ext/notification/android/AndroidUnicast.php' ));
require_once (app_path ( 'ext/notification/android/AndroidCustomizedcast.php' ));
require_once (app_path ( 'ext/notification/ios/IOSBroadcast.php' ));
require_once (app_path ( 'ext/notification/ios/IOSFilecast.php' ));
require_once (app_path ( 'ext/notification/ios/IOSGroupcast.php' ));
require_once (app_path ( 'ext/notification/ios/IOSUnicast.php' ));
require_once (app_path ( 'ext/notification/ios/IOSCustomizedcast.php' ));

class UmengPush extends ApiCommon {
	protected $appkey = NULL;
	protected $appMasterSecret = NULL;
	protected $timestamp = NULL;
	protected $validation_token = NULL;
	function __construct($key, $secret,$validation_token) {
		$this->appkey = $key;
		$this->appMasterSecret = $secret;
		$this->validation_token = $validation_token;
		$this->timestamp = strval ( time () );
	}
	public function sendAndroidBroadcast() {
		try {
			$brocast = new AndroidBroadcast ();
			$brocast->setAppMasterSecret ( $this->appMasterSecret );
			$brocast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$brocast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			$brocast->setPredefinedKeyValue ( "ticker", "Android broadcast ticker" );
			$brocast->setPredefinedKeyValue ( "title", "中文的title" );
			$brocast->setPredefinedKeyValue ( "text", "Android broadcast text" );
			$brocast->setPredefinedKeyValue ( "after_open", "go_app" );
			// Set 'production_mode' to 'false' if it's a test device.
			// For how to register a test device, please see the developer doc.
			$brocast->setPredefinedKeyValue ( "production_mode", "true" );
			// [optional]Set extra fields
			$brocast->setExtraField ( "test", "helloworld" );
			print ("Sending broadcast notification, please wait...\r\n") ;
			$brocast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	public function sendAndroidUnicast() {
		try {
			$unicast = new AndroidUnicast ();
			$unicast->setAppMasterSecret ( $this->appMasterSecret );
			$unicast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$unicast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			// Set your device tokens here
			$unicast->setPredefinedKeyValue ( "device_tokens", "xx" );
			$unicast->setPredefinedKeyValue ( "ticker", "Android unicast ticker" );
			$unicast->setPredefinedKeyValue ( "title", "Android unicast title" );
			$unicast->setPredefinedKeyValue ( "text", "Android unicast text" );
			$unicast->setPredefinedKeyValue ( "after_open", "go_app" );
			// Set 'production_mode' to 'false' if it's a test device.
			// For how to register a test device, please see the developer doc.
			$unicast->setPredefinedKeyValue ( "production_mode", "true" );
			// Set extra fields
			$unicast->setExtraField ( "test", "helloworld" );
			print ("Sending unicast notification, please wait...\r\n") ;
			$unicast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	public function sendAndroidFilecast() {
		try {
			$filecast = new AndroidFilecast ();
			$filecast->setAppMasterSecret ( $this->appMasterSecret );
			$filecast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$filecast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			$filecast->setPredefinedKeyValue ( "ticker", "Android filecast ticker" );
			$filecast->setPredefinedKeyValue ( "title", "Android filecast title" );
			$filecast->setPredefinedKeyValue ( "text", "Android filecast text" );
			$filecast->setPredefinedKeyValue ( "after_open", "go_app" ); // go to app
			print ("Uploading file contents, please wait...\r\n") ;
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents ( "aa" . "\n" . "bb" );
			print ("Sending filecast notification, please wait...\r\n") ;
			$filecast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	public function sendAndroidGroupcast() {
		try {
			/*
			 * Construct the filter condition:
			 * "where":
			 * {
			 * "and":
			 * [
			 * {"tag":"test"},
			 * {"tag":"Test"}
			 * ]
			 * }
			 */
			$filter = array (
					"where" => array (
							"and" => array (
									array (
											"tag" => "test" 
									),
									array (
											"tag" => "Test" 
									) 
							) 
					) 
			);
			
			$groupcast = new AndroidGroupcast ();
			$groupcast->setAppMasterSecret ( $this->appMasterSecret );
			$groupcast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$groupcast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			// Set the filter condition
			$groupcast->setPredefinedKeyValue ( "filter", $filter );
			$groupcast->setPredefinedKeyValue ( "ticker", "Android groupcast ticker" );
			$groupcast->setPredefinedKeyValue ( "title", "Android groupcast title" );
			$groupcast->setPredefinedKeyValue ( "text", "Android groupcast text" );
			$groupcast->setPredefinedKeyValue ( "after_open", "go_app" );
			// Set 'production_mode' to 'false' if it's a test device.
			// For how to register a test device, please see the developer doc.
			$groupcast->setPredefinedKeyValue ( "production_mode", "true" );
			print ("Sending groupcast notification, please wait...\r\n") ;
			$groupcast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	public function sendAndroidCustomizedcast() {
		try {
			$customizedcast = new AndroidCustomizedcast ();
			$customizedcast->setAppMasterSecret ( $this->appMasterSecret );
			$customizedcast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$customizedcast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then
			// use file_id to send customized notification.
			$customizedcast->setPredefinedKeyValue ( "alias", "xx" );
			// Set your alias_type here
			$customizedcast->setPredefinedKeyValue ( "alias_type", "xx" );
			$customizedcast->setPredefinedKeyValue ( "ticker", "Android customizedcast ticker" );
			$customizedcast->setPredefinedKeyValue ( "title", "Android customizedcast title" );
			$customizedcast->setPredefinedKeyValue ( "text", "Android customizedcast text" );
			$customizedcast->setPredefinedKeyValue ( "after_open", "go_app" );
			print ("Sending customizedcast notification, please wait...\r\n") ;
			$customizedcast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	public function sendIOSBroadcast() {
		try {
			$brocast = new IOSBroadcast ();
			$brocast->setAppMasterSecret ( $this->appMasterSecret );
			$brocast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$brocast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			
			$brocast->setPredefinedKeyValue ( "alert", "IOS 广播测试" );
			$brocast->setPredefinedKeyValue ( "badge", 0 );
			$brocast->setPredefinedKeyValue ( "sound", "chime" );
			// Set 'production_mode' to 'true' if your app is under production mode
			$brocast->setPredefinedKeyValue ( "production_mode", "false" );
			// Set customized fields
			$brocast->setCustomizedField ( "test", "helloworld" );
			print ("Sending broadcast notification, please wait...\r\n") ;
			$brocast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	/**
	 * ios单播推送
	 * @author:wang.hongli
	 * @param string $alert 通知内容
	 * @param string $production_mode 是否为生产环境
	 * @param string $type 消息类型 1评论 2转发 3赞 4收藏5收到私信7,到期提醒
	 * @param number $badge 消息数量
	 */
	public function sendIOSUnicast($alert='',$production_mode=false,$type='',$badge=1) {
		try {
			$unicast = new IOSUnicast ();
			$unicast->setAppMasterSecret ( $this->appMasterSecret );
			$unicast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$unicast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			// Set your device tokens here
			$unicast->setPredefinedKeyValue ( "device_tokens",  $this->validation_token);
			$unicast->setPredefinedKeyValue ( "alert", $alert );
			$unicast->setPredefinedKeyValue ( "badge", $badge );
			$unicast->setPredefinedKeyValue ( "sound", "default" );
			// Set 'production_mode' to 'true' if your app is under production mode
			$unicast->setPredefinedKeyValue ( "production_mode", $production_mode );
			// Set customized fields
			//消息类型
			$unicast->setCustomizedField ( "messageType", $type );
			$unicast->send ();
		} catch ( Exception $e ) {
			return;
// 			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	public function sendIOSFilecast() {
		try {
			$filecast = new IOSFilecast ();
			$filecast->setAppMasterSecret ( $this->appMasterSecret );
			$filecast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$filecast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			
			$filecast->setPredefinedKeyValue ( "alert", "IOS 文件播测试" );
			$filecast->setPredefinedKeyValue ( "badge", 0 );
			$filecast->setPredefinedKeyValue ( "sound", "chime" );
			// Set 'production_mode' to 'true' if your app is under production mode
			$filecast->setPredefinedKeyValue ( "production_mode", "false" );
			print ("Uploading file contents, please wait...\r\n") ;
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents ( "aa" . "\n" . "bb" );
			print ("Sending filecast notification, please wait...\r\n") ;
			$filecast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	public function sendIOSGroupcast() {
		try {
			/*
			 * Construct the filter condition:
			 * "where":
			 * {
			 * "and":
			 * [
			 * {"tag":"iostest"}
			 * ]
			 * }
			 */
			$filter = array (
					"where" => array (
							"and" => array (
									array (
											"tag" => "iostest" 
									) 
							) 
					) 
			);
			
			$groupcast = new IOSGroupcast ();
			$groupcast->setAppMasterSecret ( $this->appMasterSecret );
			$groupcast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$groupcast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			// Set the filter condition
			$groupcast->setPredefinedKeyValue ( "filter", $filter );
			$groupcast->setPredefinedKeyValue ( "alert", "IOS 组播测试" );
			$groupcast->setPredefinedKeyValue ( "badge", 0 );
			$groupcast->setPredefinedKeyValue ( "sound", "chime" );
			// Set 'production_mode' to 'true' if your app is under production mode
			$groupcast->setPredefinedKeyValue ( "production_mode", "false" );
			print ("Sending groupcast notification, please wait...\r\n") ;
			$groupcast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
	public function sendIOSCustomizedcast() {
		try {
			$customizedcast = new IOSCustomizedcast ();
			$customizedcast->setAppMasterSecret ( $this->appMasterSecret );
			$customizedcast->setPredefinedKeyValue ( "appkey", $this->appkey );
			$customizedcast->setPredefinedKeyValue ( "timestamp", $this->timestamp );
			
			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then
			// use file_id to send customized notification.
			$customizedcast->setPredefinedKeyValue ( "alias", "xx" );
			// Set your alias_type here
			$customizedcast->setPredefinedKeyValue ( "alias_type", "xx" );
			$customizedcast->setPredefinedKeyValue ( "alert", "IOS 个性化测试" );
			$customizedcast->setPredefinedKeyValue ( "badge", 0 );
			$customizedcast->setPredefinedKeyValue ( "sound", "chime" );
			// Set 'production_mode' to 'true' if your app is under production mode
			$customizedcast->setPredefinedKeyValue ( "production_mode", "false" );
			print ("Sending customizedcast notification, please wait...\r\n") ;
			$customizedcast->send ();
			print ("Sent SUCCESS\r\n") ;
		} catch ( Exception $e ) {
			print ("Caught exception: " . $e->getMessage ()) ;
		}
	}
}