<?php 
/**
*	评委
*	@author:zhang.zongliang
*	@since:2015/04/25
**/
class ApiJury extends ApiCommon
{
	
	/**
	*	获取评委列表
	**/
	public function getList($where,$page=1,$page_size=10)
	{
		$data = array();
		$where_str = '';
		if(isset($where['status'])){
			$where_str.=" and status = '".$where['status']."'";
		}
		if(isset($where['type'])){
			$where_str.=" and type = '".$where['type']."'";
		}
		if(isset($where['level'])){
			$where_str.=" and level = '".$where['level']."'";
		}
		
		$limit = '';
		if($page>0 && $page_size>0){
			$skip = ($page-1)*$page_size;
			$limit = " limit ".$skip.",".$page_size;
		}
		
		$sql="select * from jury where 1 ".$where_str." order by sort asc ".$limit;
		$rlt=DB::select($sql);
		foreach($rlt as $k=>$v){
			$v['thumb'] = $this->poem_url.$v['thumb'];
			$data[$v['level']][]=$v;
		}
		
		
		return array('code'=>1,'data'=>$data,'msg'=>'操作成功');
	}
	
	/*
	* 赛事评委列表
	*/
	public function getMatchJury($where){
		$data = array();
		$all_level=array(1=>'总决赛评委',2=>'分赛区评委',3=>'赛区评委');
		$where_str = '';
		if(isset($where['type'])){
			$where_str.=" and type='".$where['type']."'";
		}
		if(isset($where['status'])){
			$where_str.=" and status = '".$where['status']."'";
		}
		$sql="select * from jury where 1 ".$where_str." order by sort asc";
		$rlt=DB::select($sql);
		$all = array();
		foreach($rlt as $v){
			$v['thumb'] = $this->poem_url.$v['thumb'];
			$all[$v['level']][]=$v;
		}
		foreach($all as $k=>$v){
			$data[]=array(
				'name'=>$all_level[$k],
				'list'=>$all[$k],
			);
		}
		return array('code'=>1,'data'=>$data,'msg'=>'操作成功');
		
	}


}
 ?>