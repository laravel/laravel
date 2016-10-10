<?php 
/**
 * 诗文比赛控制器
 * @author drewin
 * @since:2016/04/27
 *
 */
class ApiPoetryController extends ApiCommonController {
	
	private $apiPoetry = '';
	public function __construct(){
		parent::__construct();
		$this->apiPoetry = new ApiPoetry();
	}
	/*
	 * 用户提交诗文比赛
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function addOpusPoetry(){
		$data = dealPostData();
		$rs = $this->apiPoetry->addOpusPoetry($data);
		if($rs === 'nolog'){
			$this->setReturn(-100,'请登录');
		}elseif(is_array($rs)){
			$this->setReturn(1,'success',$rs);
		}else{
			$this->setReturn(0,$rs);
		}
	}
	
	/**
	 * 删除诗文比赛作品
	 * @author:wang.hongli
	 * @since:2016/04/28
	 */
	public function delOpusPoetry(){
		if(!Input::has('id')){
			$this->setReturn(0,'作品id不能为空');
			return;
		}
		$id = intval(Input::get('id'));
		$rs = $this->apiPoetry->delOpusPoetry($id);
		if($rs === 'nolog'){
			$this->setReturn(-101,'未登录');
		}elseif($rs === true){
			$this->setReturn(1);
		}else{
			$this->setReturn(0,$rs);
		}
	}
	
	/**
	 * 获取诗文比赛参赛作品
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function getOpusPoetryList(){
		$competitionId = intval(Input::get('competitionId'));
		if(empty($competitionId)){
			$this->setReturn(0,'请查看比赛是否正确');
			return;
		}
		$pageIndex = Input::has('pageIndex')? intval(Input::get('pageIndex')):1;
		$count = Input::has('count') ? intval(Input::get('count')):20;
		$offSet = ($pageIndex-1)*$count;
		$count++;
		$rs = $this->apiPoetry->getOpusPoetryList($competitionId, $offSet, $count);
		if($rs === 'nolog'){
			$this->setReturn(-101,'未登录');
		}elseif(is_array($rs)){
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
			$this->setReturn(1,'success',$rs,$hasmore);
		}else{
			$this->setReturn(1);
		}
	}
	
	/**
	 * 诗文比赛查看数增加
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function viewOpusPoetry(){
		if(!Input::has('id')){
			$this->setReturn(1);
			return;
		}
		$id = intval(Input::get('id'));
		$rs = $this->apiPoetry->viewOpusPoetry($id);
		if($rs === 'nolog'){
			$this->setReturn(-101,'请登录');
			return;
		}
		$this->setReturn(1,'success',$rs);
	}
	
	/**
	 * 诗文比赛作品转发数增加
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function repostOpusPoetry(){
		if(!Input::has('id')){
			$this->setReturn(1);
			return;
		}
		$id = intval(Input::get('id'));
		$this->apiPoetry->repostOpusPoetry($id);
		$this->setReturn(1);
	}
	
	/**
	 * 诗文比赛作品赞数增加
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function praiseOpusPoetry(){
		if(!Input::has('id')){
			$this->setReturn(1);
			return;
		}
		$id = intval(Input::get('id'));
		$flag = intval(Input::get('flag'));
		$rs = $this->apiPoetry->praiseOpusPoetry($id,$flag);
		if($rs == 1){
			$this->setReturn(0);
			return;
		}
		$this->setReturn(1);
	}
	
	/**
	 * 诗文比赛评论
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function opusPoetryComment(){
		$data = dealPostData();
		if(empty($data)){
			$this->setReturn(0,'您提交的评论错误，请重试');
			return;
		}
		$rs = $this->apiPoetry->opusPoetryComment($data);
		if($rs == -101){
			$this->setReturn(-101,'请登录');
			return;
		}
		if(is_numeric($rs)){
			$this->setReturn(1,'success',$rs);
			return;
		}
		$this->setReturn(0,$rs);
	}
	
	/**
	 * 获取评论列表
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function getOpusPoetryCommentList(){
		if(!Input::has('opusid')){
			$this->setReturn(0,'作品id不能为空');
			return;
		}
		$count = Input::has('count') ? intval(Input::get('count')) : 20;
		$pageIndex = Input::has("pageIndex") ? intval(Input::get('pageIndex')) : 1;
		$offSet = ($pageIndex-1)*$count;
		$count++;
		$opusId = intval(Input::get('opusid'));
		$rs = $this->apiPoetry->getOpusPoetryCommentList($opusId,$count,$offSet);
		if($rs == 'nolog'){
			$this->setReturn(-101,'请登录');	
			return;
		}
		$hasmore = 0;
		if(!empty($rs)){
			$hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
		}
		$this->setReturn(1,'success',$rs,$hasmore);
	}
	
	/**
	 * 删除作品评论
	 * @author:wang.hongli
	 * @since:2016/04/27
	 */
	public function delOpusPoetryComment(){
		if(!Input::has('id')){
			$this->setReturn(0,'删除作品失败');
			return;
		}
		$id = intval(Input::get('id'));
		$rs = $this->apiPoetry->delOpusPoetryComment($id);
		if($rs === 'nolog'){
			$this->setReturn(-101,'未登录');
			return;
		}
		if($rs === true){
			$this->setReturn(1);
		}else{
			$this->setReturn(0,$rs);
		}
	}
	
	/**
	 * 诗文比赛--获取自己诗文列表
	 * @author:wang.hongli
	 * @since:2016/05/11
	 */
	public function getSelfOpusPoetry(){
		if(!Input::has('competitionid')){
			$this->setReturn(0,'比赛不存在');
			return;
		}
		$competitionid = intval(Input::get('competitionid'));
		$rs = $this->apiPoetry->getSelfOpusPoetry($competitionid);
		if($rs === 'nolog'){
			$this->setReturn(-101,'未登录');
			return;
		}
		$this->setReturn(1,'success',$rs);
	}
}
 ?>