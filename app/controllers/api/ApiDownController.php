<?php 

/**
* 下载控制器
**/
class ApiDownController extends ApiCommonController {
	private $ApiDown;
	private $message;
	function __construct(){
		$this->ApiDown = new ApiDown();
		$this->message=Lang::get('messages.nolog');
	}
	
	//添加下载信息
	public function addDownInfo(){
		$rs = $this->ApiDown->addDownInfo();
		if('nolog' === $rs) {
			$this->setReturn(-100,'nolog',$this->message);return;
		} elseif("opus_error"=== $rs) {
			$this->setReturn(-200,"error",'作品id错误');return;
		}elseif("nojewel"=== $rs) {
			$this->setReturn(-1,"nojewel",'钻石不足');return;
		} elseif(true=== $rs) {
			$this->setReturn(1,"succsee",'添加成功',$rs);return;
		}  
	}

//下载提示信息
	public function down_message(){

		$user_info = ApiCommonStatic::viaCookieLogin();
	
		if(!$user_info){
			$this->setReturn(-100,'nolog',$this->message);return ;
		}
		$opusid= intval(Input::get('opusid',0))	;
		if($opusid ==0){
			$this->setReturn(-1,'no_opudis');return ;
		}
		$rs = $this->ApiDown->down_message($user_info['id'],$opusid);
		if($rs==="free"){
			$this->setReturn(1,'本次免费下载');return;
		}elseif($rs==="jewel"){
			$this->setReturn(2,'本次下载花费钻石',Config::get('app.down_money'));return;
		}elseif($rs==="down_num"){
			$num=DB::table('down_opus_limit')->where('uid',$user_info['id'])->pluck('down_num');  
			$this->setReturn(3,'本次下载花费会员下载次数',$num);return;
		}
		
	}


	//下载信息列表
	public function showDownInfo() {
		 
		$rs = $this->ApiDown->showDownInfo();
		if('nolog' === $rs) {
			$this->setReturn(-100,'nolog',$this->message);
		} else {
				$hasmore =$rs['hasmore'];
				unset($rs['hasmore']);
			$this->setReturn(1,"信息列表",$rs,$hasmore);
		}
	}
	
	//下载信息删除
	public function delDownOne() {
		$rs = $this->ApiDown->delDownOne();
		$this->setReturn(1,$rs);
		if('nolog' === $rs) {
			$this->setReturn(-100,'nolog',$this->message);
		} else if($rs ==='opus_error') {
			$this->setReturn(0,'参数错误');
		}else if($rs ===true) {
			$this->setReturn(1,'删除成功');
		} else  if($rs ===false){
			$this->setReturn(2,'删除失败');
		}
	}
	
	
}