<?php
/**
 * 班级活动控制器
 * @author:wang.hongli
 * @since:2016/05/25
 */
class ApiClassActive extends ApiCommon {
	
	/**
	 * 获取活动信息
	 * @author:wang.hongli
	 * @since:2016/05/31
	 */
	public function getClassActiveInfo($data){
		$rules = array(
				'competitionId'=>'required|integer',
		);
		$message = array(
				'competitionId.required'=>'请填写比赛id',
				'competitionId.integer'=>'比赛格式错误'
		);
		
		$validator = Validator::make($data, $rules,$message);
		if($validator->fails()){
			$m  = $validator->messages();
			$msg = $m->all();
			return $msg[0];
		}
		$competitionId = intval($data['competitionId']);
		$rs = DB::table('class_active')->where('id',$competitionId)->first(array('id','name','desc','pid','mainpic','piclist','starttime','endtime','clause_title','clause','has_invitecode'));
		if(empty($rs)){
			return '获取比赛失败';
		}
		if(!empty($rs['mainpic'])){
			$rs['mainpic'] = $this->poem_url.'/'.$rs['mainpic'];
		}
		$last_piclist = array();
		if(!empty($rs['piclist'])){
			$tmp_piclist = unserialize($rs['piclist']);
			if(!empty($tmp_piclist)){
				foreach($tmp_piclist as $key=>&$value){
					$last_piclist[] = $this->poem_url.'/'.$value;
				}
			}
			$rs['piclist'] = !empty($last_piclist) ? $last_piclist : array();
		}
		//根据比赛id获取产品信息
		$apiGoods = new ApiGoods();
		$goodInfo = $apiGoods->accorCompIdGetGoodsInfo(1,$competitionId);
		if(empty($goodInfo)){
			$goodInfo['id'] = 0;
		}
		$rs['goods_id'] = $goodInfo['id'];
		return $rs;
	}
	/**
	 * 班级活动表单添加
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public function joinClassActive($data=array()){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$uid = intval($info['id']);
		$pattern = '/\s/i';
		if(!empty($data['email'])){
			$data['email'] = preg_replace($pattern, '', $data['email']);
		}
		$rules = array(
				'name'=>'required|alpha_dash',
				'gender'=>'required|in:0,1',
				'card'=>'required|alpha_num',
				'company'=>'required',
				'province_id'=>'required|integer',
				'city_id'=>'required|integer',
				'area_id'=>'required|integer',
				'address'=>'required',
				'zip'=>'required',
				'mobile'=>'required|alpha_num',
				'email'=>'required|email',
				'birthday'=>'required|date',
				'competition_id'=>'required|integer',
		);
		$message = array(
				'name.required'=>'请填写用户名',
				'name.alpha_dash'=>'用户名格式错误',
				'gender.required'=>'请填写性别',
				'gender.in'=>'性别错误',
				'card.required'=>'请填写身份证号',
				'card.alpha_num'=>'身份证号只能为字母，数字',
				'company.required'=>'请填写工作单位',
				'province_id.required'=>'请选择省份',
				'province_id.integer'=>'省份错误',
				'city_id.required'=>'请选择城市',
				'city_id.integer'=>'城市错误',
				'area_id.required'=>'请填写地区',
				'area_id.integer'=>'地区错误',
				'address.required'=>'详细地址不能为空',
				'zip.required'=>'请填写邮编',
				'mobile.required'=>'请填写手机号',
				'mobile.alpha_num'=>'手机号只能为字母数字',
				'email.required'=>'请填写电子邮件',
				'email.email'=>'电子邮件格式错误',
				'birthday.required'=>'请填写生日',
				'birthday.date'=>'生日格式错误',
				'competition_id.required'=>'请填写活动id',
				'competition_id.integer'=>'活动id格式错误'
		);
		$validator = Validator::make($data, $rules,$message);
		if($validator->fails()){
			$msg = $validator->messages()->first();
			return $msg;
		}
		$data['uid'] = $uid;
		$data['nick'] = $info['nick'];
		$data['addtime'] = time();
		$data['status'] = 0;
		$data['birthday'] = strtotime($data['birthday']);
		$data['company'] = htmlspecialchars($data['company']);
		$data['address'] = htmlspecialchars($data['address']);
		$data['age'] = accorCardGetAge($data['card']);
		unset($data['_token']);
		if(!empty($data['invitationcode'])){
			$codes = DB::table('invitecode')->where('status',2)->lists('code');
			if(!empty($codes)){
				if(!in_array($data['invitationcode'], $codes)){
					return '参赛码错误，请重试';
				}
			}
		}
		try {
			$id = DB::table('class_active_form')->insertGetId($data);
			if($id){
				return $id;
			}else{
				return '添加错误，请重试';
			}
		} catch (Exception $e) {
			return '添加错误，请重试';
		}		
	}
	
	/**
	 * 用户获取最近添加过的表单信息
	 * @author:wang.hongli
	 * @since:2016/05/25
	 */
	public  function getClassActiveUserInfo(){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nologin';
		}
		$uid = $info['id'];
		$rs = DB::table('class_active_form')
				->select('id','uid','name','gender','card','company','province_id','city_id','area_id','address','zip','mobile','email','birthday')
				->where('uid',$uid)->orderBy('id','desc')->first();
		if(empty($rs)){
			return array();
		}
		$rs['nick'] = $info['nick'];
		$rs['birthday'] = date('Y-m-d',$rs['birthday']);
		return $rs;
	}
}