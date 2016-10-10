<?php 
/**
*	验证码类
*	@author:wang.hongli
*	@since:2015/07/11
**/
class AdminValidateCode
{
	//随机数
	private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
	private $code; //验证码
	private $codelen = 4;//验证码长度
	private $width = 130;//宽度
	private $height = 50;//高度
	private $img; //图像资源句柄
	private $font;//指定的字体
	private $fontsize=20;//指定字体大小
	private $fontcolor;//指定字体颜色
	//构造方法
	public function __construct($codelen=4,$width=130,$height=50,$fontsize=20)
	{
		// $url = Config::get('app.url');
		$url = '/www/poem/public';
		$this->codelen = $codelen;
		$this->width=$width;
		$this->height=$height;
		$this->fontsize = $fontsize;
		$this->font=$url.'/font/'.'/Elephant.ttf';
	}
	//生成随机数
	private function createCode()
	{
		$_len = strlen($this->charset)-1;
		for($i=0;$i<$this->codelen;$i++)
		{
			$this->code .= $this->charset[mt_rand(0,$_len)];
		}
		session_start();
		unset($_SESSION[md5('dushivalcode')]);
		$valcode = md5('dushivalcode');
		$_SESSION[$valcode] = strtolower($this->code);
	}
	
	//背景
	private function createBg()
	{
		$this->img = imagecreatetruecolor($this->width, $this->height);
		$color = imagecolorallocate($this->img, mt_rand(157,255), mt_rand(157,255),mt_rand(157,255));
		imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
	}

	 //生成文字
	private function createFont() 
	{
		$_x = $this->width / $this->codelen;
	  	for ($i=0;$i<$this->codelen;$i++) 
	  	{
	   		$this->fontcolor = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
	   		// imagettftext($this->img,$this->fontsize,mt_rand(-20,20),$_x*$i+mt_rand(1,5),$this->height / 1.4,$this->fontcolor,$this->font,$this->code[$i]);
	   		imagestring($this->img, 5,$_x*$i+mt_rand(1,5) , $this->height/5, $this->code[$i], $this->fontcolor);
	  	}
	}
	//生成线条、雪花
 	private function createLine() 
 	{
	  	//线条
	  	for ($i=0;$i<6;$i++) 
	  	{
	   		$color = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
	   		imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);
	  	}
	  	//雪花
	  	for ($i=0;$i<100;$i++) 
	  	{
	   		$color = imagecolorallocate($this->img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
	   		imagestring($this->img,mt_rand(1,5),mt_rand(0,$this->width),mt_rand(0,$this->height),'*',$color);
	  	}
 	}
	//输出
	private function outPut() 
	{
		header("Cache-Control: no-cache, must-revalidate");
	  	header('Content-Type:image/png');
	  	imagepng($this->img);
	  	imagedestroy($this->img);
	}
 	//对外生成
 	public function doimg() 
 	{
	  	$this->createBg();
	  	$this->createCode();
	  	$this->createLine();
	  	$this->createFont();
	  	$this->outPut();
	  	die;
 	}
 	//获取验证码
 	public function getCode()
 	{
  		return strtolower($this->code);
 	}
}
?>