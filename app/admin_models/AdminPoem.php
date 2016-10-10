<?php
use Illuminate\Support\Facades\Validator;

/**
 * 后台伴奏model
 *
 * @author :wang.hongli
 * @since :2016/03/30
 *       
 */
class AdminPoem extends AdminCommon {
	
	/**
	 * 后台修改伴奏说字母或者拼音首字母
	 *
	 * @author :wang.hongli
	 * @since :2016/03/30
	 * @param
	 *        	:char:首字母或者拼音首字母，poemid 伴奏id flag 1 首字母 2拼音首字母
	 *        	
	 */
	public function modifyPoemChar($char = '', $poemid = 0, $flag = 1) {
		// 验证规则
		$rules = array (
				'char' => 'required|alpha',
				'poemid' => 'required|integer',
				'flag' => 'required|integer' 
		);
		// 验证数据
		$data = array (
				'char' => $char,
				'poemid' => $poemid,
				'flag' => $flag 
		);
		// 验证方法
		$validator = Validator::make ( $data, $rules );
		if ($validator->fails ()) {
			return false;
		}
		switch ($flag) {
			case 1 :
				DB::table ( 'poem' )->where ( 'id', $poemid )->update ( array (
						'spelling' => $char 
				) );
				break;
			case 2 :
				DB::table ( 'poem' )->where ( 'id', $poemid )->update ( array (
						'allchar' => $char 
				) );
				break;
		}
		return true;
	}
}
?>