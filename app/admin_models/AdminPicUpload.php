<?php 
/**
*	后台图片压缩处理
*	@author:wang.hongli
*	@since:2015/05/09
*/
class AdminPicUpload extends AdminCommon
{
	/**
	*	上传图片
	*	@author:wang.hongli
	*	@since:2015/05/09
	*	@param:source 图片资源,$wh 100_200 100宽 200 高 缩放到指定大小,$despath 目标目录
	**/
	public function upload($source='',$despath='',$fileName='',$wh='100_100')
	{
		if(empty($source) || empty($despath))
		{
			return false;
		}
		
		$tmp_arr = explode('_', $wh);
		$des_w = $tmp_arr[0];
		$des_h = $tmp_arr[1];

		//创建目录
		$fileDir = $this->isExistDir($despath);
		$allow_ext = array('jpeg','jpg','gif','png');
		$ext = $source->guessExtension();
		if(!in_array($ext, $allow_ext))
		{
			return false;
		}
		//原图真实路径
		$realPath = $source->getRealPath();
		$image_size_arr = getimagesize($realPath);
		if(!$image_size_arr)
		{
			return false;
		}
		$src_w = $image_size_arr[0];
		$src_h = $image_size_arr[1];
		$fileName = $fileName.'.'.$ext;
		$desc_file_name = $wh.'_'.$fileName;
		//保存原图
		$source->move($despath,$fileName);
		//原图片尺寸与目标尺寸对比
		$src_whole_name = $despath.'/'.$fileName;
		$desc_whole_name = $despath.'/'.$desc_file_name;
		if($src_w<=$des_w)
		{
			$source->move($despath,$desc_file_name);
		}
		else
		{
			//进行等比例缩放
			$des_h = round($des_w*$src_h/$src_w);
			Image::make($src_whole_name)->resize($des_w,$des_h)->save($desc_whole_name);
		}
		return $src_whole_name;
	}
}
 ?>