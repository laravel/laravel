<?php 
/**
*	后台所有导入程序
*	@author:wang.hongli
*	@since:2015/01/04
*/
class AdminImportController extends BaseController
{
	/**
	 * 
	 */
	public function importPoem(){
		$id = $_GET['id'];
		if(empty($id)){
			echo '执行错误，请重试';
			return false;
		}
		$excelName = DB::table('poem_xls')->where('id',$id)->pluck('name');
		$adminImport = new AdminImport();
		//exelpath
		$excelpath = public_path('importexcel/'.$excelName);
		$is_successs = $adminImport->importPoem($excelpath);
		if($is_successs === true){
			DB::table('poem_xls')->where('id',$id)->update(array('status'=>2));	
			echo  1;
			return;
		}
		echo $is_successs;
			
	}
}