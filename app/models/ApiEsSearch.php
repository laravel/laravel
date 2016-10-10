<?php 
/**
 * 搜索接口 -- elasticsearch
 * @author :wang.hongli
 * @since :2016/07/24
 */
class ApiEsSearch extends ApiCommon{

	/**
	 * 搜索作品
	 * @author :wang.hongli
	 * @since :2016/07/24
	 */
	public function getClient(){
		$hosts = Config::get('elasticsearch.hosts');
		$handlerParams = Config::get('elasticsearch.handlerParams');
		$client = Config::get('elasticsearch.client');
		$retries=Config::get('elasticsearch.retries');
		$params = [
			'hosts'=>$hosts,
			'retries'=>$retries,
			'handler'=>Elasticsearch\ClientBuilder::singleHandler($handlerParams)
		];
		$connect = Elasticsearch\ClientBuilder::fromConfig($params,true);
		return $connect;
	}
	/**
	 * 搜索作品
	 * @author :wang.hongli
	 * @since :2016/07/25
	 */
	public function searchOpus($params=['keywords'=>'','pageIndex'=>1,'count'=>20]){
		if(empty($params)){
			return [];
		}
		$keywords = trim($params['keywords']);
		if(empty($keywords)){
			return [];
		}
		$pageIndex = !empty($params['pageIndex']) ? intval($params['pageIndex']) : 1;
		$count = !empty($params['count']) ? intval($params['count']) : 20;
		$offSet = ($pageIndex-1)*$count;
		$count++;
		$client = $this->getClient();
		$opus_param = Config::get('elasticsearch.opus_param');
		$opus_param['body'] = [
			'query'=>[
				"dis_max"=>[
					"queries"=>[
						['match'=>['name'=>$keywords]],
						['match'=>['pinyin'=>$keywords]]
					]
				]
			],
			'sort'=>[
				['_score'=>['order'=>'desc']],
				['id'=>['order'=>'desc']]
			]
		];
		$opus_param['from'] = $offSet;
		$opus_param['size'] = $count;
		$rs = $client->search($opus_param);
		$returnids = [];
		if(isset($rs['hits']['hits'])){
			$rs = $rs['hits']['hits'];
			if(!empty($rs)){
				foreach($rs as $k=>$v){
					$returnids[] = $v['_id'];
				}
			}
		}
		return $returnids;
	}
	
	/**
	 * 搜索用户
	 * @author :wang.hongli
	 * @since :2016/07/26
	 */
	public function searchUser($params=['keywords'=>'','pageIndex'=>1,'count'=>20]){
		if(empty($params['keywords'])){
			return [];
		}
		$keywords = trim($params['keywords']);
		$pageIndex = !empty($params['pageIndex']) ? intval($params['pageIndex']) : 1;
		$count = !empty($params['count']) ? intval($params['count']) : 20;
		$offSet = ($pageIndex-1)*$count;
		$count++;
		$client = $this->getClient();
		$user_param = Config::get('elasticsearch.user_param');

		$user_param['body'] = [
			'query'=>[
				"dis_max"=>[
					"queries"=>[
						['match'=>['nick'=>$keywords]],
						['match'=>['pinyin'=>$keywords]]
					]
				]
			],
			'sort'=>[
				['_score'=>['order'=>'desc']],
				['id'=>['order'=>'asc']]
			]
		];
		$user_param['from'] = $offSet;
		$user_param['size'] = $count;
		
		$rs = $client->search($user_param);
		$returnids = [];
		if(isset($rs['hits']['hits'])){
			$rs = $rs['hits']['hits'];
			if(!empty($rs)){
				foreach($rs as $k=>$v){
					$returnids[] = $v['_id'];
				}
			}
		}
		return $returnids;
	}

	/**
	 * 搜索伴奏
	 * @author :wang.hongli
	 * @since :2016/07/25
	 */
	public function searchPoem($params=['keywords'=>'','pageIndex'=>1,'count'=>20]){
		if(empty($params['keywords'])){
			return [];
		}
		$keywords = trim($params['keywords']);
		$pageIndex = !empty($params['pageIndex']) ? intval($params['pageIndex']) : 1;
		$count = !empty($params['count']) ? intval($params['count']) : 20;
		$offSet = ($pageIndex-1)*$count;
		$count++;
		$client = $this->getClient();
		$poem_param = Config::get('elasticsearch.poem_param');
		$poem_param['body'] = [
			'query'=>[
				"dis_max"=>[
					"queries"=>[
						['match'=>['readername'=>$keywords]],
						['match'=>['name'=>$keywords]],
						['match'=>['aliasname'=>$keywords]],
						['match'=>['allchar'=>$keywords]],
						['match'=>['writername'=>$keywords]],
						['match'=>['spelling'=>$keywords]],
						['match'=>['readerallchar'=>$keywords]],
						['match'=>['writerallchar'=>$keywords]]
					]
				]
			],
			'sort'=>[
				['_score'=>['order'=>'desc']],
				['id'=>['order'=>'desc']]
			]
		];
		$poem_param['from'] = $offSet;
		$poem_param['size'] = $count;
		$rs = $client->search($poem_param);
		$returnids = [];
		if(isset($rs['hits']['hits'])){
			$rs = $rs['hits']['hits'];
			if(!empty($rs)){
				foreach($rs as $k=>$v){
					$returnids[] = $v['_id'];
				}
			}
		}
		return $returnids;
	}
	/**
	 * 向ES中增加用户
	 * @author :wang.hongli
	 * @since :2016/07/28
	 */
	public function addEsUser($data=[]){
		if(empty($data)){
			return false;
		}
		$client=$this->getClient();
		$user_param = [
			'index'=>'poem',
			'type'=>'user',
			'id'=>$data['id'],
			'body'=>[
				'id'=>$data['id'],
				'nick'=>$data['nick'],
				'pinyin'=>$data['pinyin']
			]
		];
		try {
			$client->index($user_param);
		} catch (Exception $e) {
			
		}
		return;
	}

	/**
	 *  更新用户昵称，昵称拼音
	 *  @author :wang.hongli
	 *  @since :2016/08/02
	 */
	public function updateEsUser($data=['id'=>0,'nick'=>'','pinyin'=>'']){
		if(empty($data['nick']) || empty($data['id'])){
			return false;
		}
		$client = $this->getClient();
		$user_param = [
			'index'=>'poem',
			'type'=>'user',
			'id'=> $data['id'],
			'body'=>[
				'doc'=>[
					'nick'=>$data['nick'],
					'pinyin'=>$data['pinyin']
				]
			]
		];
		try {
			$client->update($user_param);
		} catch (Exception $e) {
			
		}
		return;
	}
	/**
	 * 删除ES中用户
	 * @author :wang.hongli
	 * @since :2016/07/28
	 */
	public function delEsUser($id){
		$client = $this->getClient();
		$user_param = [
			'index'=>'poem',
			'type'=>'user',
			'id'=>$id
		];
		try {
			$client->delete($user_param);
			//删除用户所有作品
			$opus_ids = DB::table('opus')->where('uid',$id)->lists('id');
			if(empty($opus_ids)){
				return;
			}
			foreach($opus_ids as $k=>$v){
				$opus_param = [
					'index'=>'poem',
					'type'=>'opus',
					'id'=>$v
				];
				$client->delete($opus_param);
			}
		} catch (Exception $e) {
		}
		return;
	}
	/**
	 * 删除ES中作品
	 * @author :wang.hongli
	 * @since :2016/07/28
	 */
	public function delEsOpus($id=0){
		if(empty($id)){
			return false;
		}
		$client = $this->getClient();
		$opus_param = [
			'index'=>'poem',
			'type'=>'opus',
			'id'=>$id
		];
		try {
			$client->delete($opus_param);
		} catch (Exception $e) {
		}
		return;
	}

	/**
	* 向ES中添加作品
	* @author :wang.hongli
	* @since :2017/07/28
	 */
	public function addEsOpus($data=[]){
		if(empty($data)){
			return false;
		}
		$client = $this->getClient();
		$opus_param = [
			'index'=>'poem',
			'type'=>'opus',
			'id'=>$data['id'],
			'body'=>[
				'id'=>$data['id'],
				'name'=>$data['name'],
				'pinyin'=>$data['pinyin']
			]
		];
		try {
			$client->index($opus_param);
		} catch (Exception $e) {
		}
		return;
	}

	/**
	 * 向ES中导入伴奏
	 * @author :wang.hongli
	 * @since :2016/07/28
	 */
	public function addEsPoem($data=[]){
		if(empty($data)){
			return false;
		}
		$client=$this->getClient();
		$poem_param = [
			'index'=>'poem',
			'type'=>'poem',
			'id'=>$data['id'],
			'body'=>[
				'id'=>$data['id'],
				'name'=>$data['name'],
				'aliasname'=>$data['aliasname'],
				'allchar'=>$data['allchar'],
				'readername'=>$data['readername'],
				'readerallchar'=>$data['readerallchar'],
				'writername'=>$data['writername'],
				'writerallchar'=>$data['writerallchar'],
				'spelling'=>$data['spelling']
			]
		];
		try {
			$client->index($poem_param);
		} catch (Exception $e) {
			
		}
		return;
	}
}


 ?>