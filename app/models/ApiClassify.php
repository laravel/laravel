<?php 
//作品分类
class ApiClassify extends ApiCommon {
	/**
	* 得到分类
	* @author:hgz
	* @since:2016/07/13
	*/
	public function getClassify( ) {
		$classify=DB::table('navigation')->select( "id","category as name")->where('pid',0)->where('type',0)->where('isdel',0)->get();
		return $classify; 
	}
	/*
	* 得到分类下的作品
	* @author:hgz
	* @since:2016/07/13
	*/
	public function getOpus($id,$pid,$offSet=0,$count=21){
	 	$nav=DB::table('navigation')->where("id",'=',$pid)->take(1)->pluck('id');
	 	if(empty($nav)){
	 		return [];
	 	}
		$list=DB::table('navigation')->where("pid",'=',$nav)->lists('id');
		//插入父级id
		$id_array = array_merge([$nav],$list);
		foreach ($list as $key => $value) {
			$id_array[]=$value;
		}
		$opusid=[];
		foreach($id_array as $k=>$v){
			$table_id = $v%10;
			$table = 'nav_opus_'.$table_id;
			$tmp_opus_id = DB::table($table)->where('uid',$id)->where('categoryid',$v)->lists('opusid');
			if(!empty($tmp_opus_id)){
				$opusid[] = $tmp_opus_id;
			}
		}
		$opusids = [];
		foreach($opusid as $k=>$v){
			if(empty($v)) continue;
			foreach($v as $kk=>$vv){
				$opusids[$vv] = $vv;
			}
		}
		if(empty($opusids)){
			return [];
		}
		$hasmore = 0;
		if(count($opusids)>=$count){
			$hasmore = 1;
		}
		$opus_list=DB::table('opus')->where('uid','=',$id)->whereIn("id",$opusids)->where('isdel',0)->orderBy("id",'desc')->skip($offSet)->take($count)->get();
		if(empty($opusid)){
			return [];
		}
		foreach($opus_list as $k=>&$v){
			$v['lyricurl'] = $this->poem_url.ltrim($v['lyricurl'],'.');
			$v['url']=$this->poem_url.trim($v['url'],'.');
		}
		$opus_list['hasmore'] = $hasmore;
		return $opus_list;
	}
}
