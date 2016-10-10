<?php
/**
 * 打赏模型
 * @author:wang.hongli
 * @since:2016/04/29
 */
class ApiReward extends ApiCommon{
	
	/**
	 * 根据商品id获取随机金额
	 * @author:wang.hongli
	 * @since:2016/04/29
	 * @param:goods_id商品id
	 */
	public function getRandomMoney($goods_id){
		$rand = array();
		for($i=1;$i<=10;$i++){
			array_push($rand, $i+0.18);
			array_push($rand, $i+0.58);
			array_push($rand, $i+0.66);
			array_push($rand, $i+0.68);
			array_push($rand, $i+0.88);
			array_push($rand, $i+0.98);
			array_push($rand, $i+0.99);
		}
		$arr = array(
				16=>array(
						'rand'=>$rand,//随机金额
						'fixation'=>array(18,58,98,118,158,198),//固定金额
						'entry_title'=>'感恩打赏',//设置入口标题
						'reward_top_title'=>'感恩“为你诵读”团队',//打赏页标题
						'reward_title'=>'你的诗意生活，“为你诵读”的努力',//打赏页内部标题
						'reward_des'=>"樱桃好吃树难栽！\n喝水不忘挖井人！\n众人拾柴火焰高！\n“为你诵读”需要大家的支持！",//打赏页描述
						'record_over_title'=>'团队很辛苦，给他们打个赏吧！',//录制完成提示框标题
						'record_over_content'=>"喝水不忘挖井人！\n众人拾柴火焰高！\n“为你诵读”需要大家的支持！",//录制完成提示框内容
						'record_finish_btn_title'=>'感恩打赏',//录制完成打赏按钮标题
						'record_refuse_btn_title'=>'无情拒绝',//录制完成拒绝按钮标题
						'flag'=>0 //打开打赏模式  1关闭打赏模式
				)
		);
		if(!empty($arr[$goods_id])){
			return $arr[$goods_id];
		}else{
			return false;
		}
	}
}