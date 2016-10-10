<?php 
/**
*	@author:wang.hongli
*	@since:2015/06/28
*	系统公共函数
**/

/**
*	根据作品id获取作品分类id
*	@author:wang.hongli
*	@param:opus_id 作品id 逗号拼接的字符串或者数字 '1,2,3'
**/

function my_getNavId($opus_id)
{
	if(empty($opus_id))
	{
		return false;
	}
	$opus_arr = explode(',', $opus_id);
}

/**
*	获取文件内容
*	@author:wang.hongli
*	@since:2015/07/03
**/
function my_get_url_contents($url)
{
	if(ini_get('allow_url_fopen') == 1)
	{
		$response = file_get_contents($url);
	}
	else
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response =  curl_exec($ch);
        curl_close($ch);
	}
	return $response;
}

/**
*	判断文件类型
*	@author:wang.hongli
*	@since:2015/07/12
*	@param:url 文件路径
**/
function my_file_type($url)
{
	if(!file_exists($url) || !is_readable($url))
		return false;
	require_once '../app/ext/getid/getid3.php';
	$getID3 = new getID3;
	$DeterminedMIMEtype = '';
	if ($fp = fopen($url, 'rb')) 
	{
		$getID3->openfile($url);
		if (empty($getID3->info['error'])) 
		{
			getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.tag.id3v2.php', __FILE__, true);
			$getid3_id3v2 = new getid3_id3v2($getID3);
			$getid3_id3v2->Analyze();
			fseek($fp, $getID3->info['avdataoffset'], SEEK_SET);
			$formattest = fread($fp, 16);  // 16 bytes is sufficient for any format except ISO CD-image
			fclose($fp);
			$DeterminedFormatInfo = $getID3->GetFileFormat($formattest);
			$DeterminedMIMEtype = $DeterminedFormatInfo['module'] ? $DeterminedFormatInfo['module'] : false;
		} else {
			//open file error
			return false;
		}
	} else {
			//open file error
			return false;
	}
	return $DeterminedMIMEtype;
}

/**
 * 获取客户端IP
 * @return String
 */
function GetIP() {
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}elseif(isset($_SERVER["HTTP_CLIENT_IP"])){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}elseif(isset($_SERVER["REMOTE_ADDR"])){
		$ip = $_SERVER["REMOTE_ADDR"];
	}elseif(getenv("HTTP_X_FORWARDED_FOR")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}elseif(getenv("HTTP_CLIENT_IP")){
		$ip = getenv("HTTP_CLIENT_IP");
	}elseif(getenv("REMOTE_ADDR")){
		$ip = getenv("REMOTE_ADDR");
	}else{
		$ip = "Unknown";
	}
	return $ip;
}

/**
*	敏感词过滤
*	@author:wang.hongli
*	@since:2015/01/04
*	@param:strContent:过滤的内容
**/
function my_sens_word($strContent='')
{
	if(empty($strContent))
	{
		return false;
	}
	$pattern = '/\[\d{1,2}\:\d{1,2}\.\d{1,2}\]/i';
	$s = preg_replace($pattern, '', $strContent);
	$path = public_path();
	$resTrie = trie_filter_load($path.'/blackword.tree');
	$arrRet = trie_filter_search($resTrie, $s);
	if(!empty($arrRet))
	{
		return true;
	}
	$pattern = '/\d{5}|\@/isU';
	if(preg_match($pattern, $strContent))
	{
		return true;
	}
	return false;
}

/**
 * 统计特定目录下文件个数
 * @author:wang.hongli
 * @since:2016/04/07
 */
function get_dir_num($path){
	if(empty($path)){
		return 0;
	}
	try {
		$dir = scandir($path);
		$poem_num = (count($dir)-1);
	} catch (Exception $e) {
		$poem_num = 0;
	}
	return $poem_num;
}

/**
 * 获取文字拼音首字母
 * @author:wang.hongli
 * @since:2016/05/13
 */
function getPinyin($name){
	if(empty($name)) return '';
	
	require_once app_path().'/commands/PinYin.php';
	$pinYin = new Pinyin ();
	$allchar = @$pinYin->getPinyin ( $name );
	if(empty($allchar)) return '';
	
	$tmpPyArr = explode ( ' ', $allchar );
	$str = null;
	foreach ( $tmpPyArr as $k => $v ) {
		if (empty ( $v )) {
			continue;
		}
		$str .= substr ( $v, 0, 1 );
	}
	return $str;
}

/**
 * 发送短信验证码
 * @author:wang.hongli
 * @since:2016/05/27
 */
function sendSMS($phone,$code){
	if(empty($phone) || empty($code)){
		return false;
	}
	set_time_limit(0);
	header('Content-type:text/html;charset=utf-8');
	define('SCRIPT_ROOT',public_path().'/packages/yimeinotev2/');
	require_once SCRIPT_ROOT.'include/Client.php';
	require_once SCRIPT_ROOT.'nusoaplib/nusoap.php';
	$gwUrl = 'http://hprpt2.eucp.b2m.cn:8080/sdk/SDKService?wsdl';
	$serialNumber = '8SDK-EMY-6699-RIQNR';
	$password = '365521';
	$sessionKey = '365521';
	$connectTimeOut = 2;
	$readTimeOut = 10;
	$proxyhost = false;
	$proxyport = false;
	$proxyusername = false;
	$proxypassword = false;
	$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
	$client->setOutgoingEncoding("UTF-8");
	//发送短信
	$statusCode = $client->sendSMS(array($phone),'【为你诵读】'.'您的验证码为'.$code.'，欢迎继续“为你诵读”！');
	$tmpArr['sign'] = 2;
	$tmpArr['code'] = $code;
	//将code放入到SESSION中
	Session::put('code',$code);
	Session::save();
	return true;
}

/**
 * @author:wang.hongli
 * @since:2016/05/28
 * @对data数据进行过滤
 */
function dealPostData(){
	$data = Input::all();
	if(isset($data['self_sign'])){
		unset($data['self_sign']);
	}
	return $data;
}

/**
*@author:wang.hongli
*@since:2016/06/27
*根据身份证号获取年龄
*/
function accorCardGetAge($card=0){
	if(empty($card)) return 0;
	$tmp_age = substr($card, 6,8);
	$age = time()-strtotime($tmp_age);
	$age  = $age>0 ? $age : 0;
	$age = floor($age/(3600*24*365));
	$age = $age>100 ? 100 : intval($age);
	return $age;
}

?>